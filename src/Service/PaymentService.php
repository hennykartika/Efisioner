<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\RuleViolation;
use App\Domain\Survey;
use App\Domain\User;
use App\Payment\CheckoutRequest;
use App\Payment\GatewayStatus;
use App\Payment\PaymentGateway;
use App\Payment\PaymentGatewayException;
use App\Repository\PaymentRepository;
use App\Repository\QuestionRepository;
use App\Repository\SurveyRepository;
use App\Support\Database;
use PDO;

/**
 * A survey only goes live after the payment provider confirms the money.
 * The browser redirect back from Midtrans is never trusted on its own; state
 * always comes from a signed notification or a server-to-server status call.
 */
final class PaymentService
{
    private const REUSE_CHECKOUT_MINUTES = 30;

    public function __construct(
        private readonly PDO $db,
        private readonly SurveyRepository $surveys,
        private readonly QuestionRepository $questions,
        private readonly PaymentRepository $payments,
        private readonly PaymentGateway $gateway,
        private readonly SurveyPricing $pricing,
    ) {
    }

    /**
     * @return string URL of the hosted payment page
     */
    public function startCheckout(Survey $survey, User $owner, string $finishUrl): string
    {
        if ($survey->isPaid()) {
            throw new RuleViolation('This survey has already been paid for.');
        }

        if ($this->questions->count($survey->id) === 0) {
            throw new RuleViolation('Add at least one question before paying.');
        }

        $amount = $this->pricing->total($survey->targetRespondents, $survey->rewardPerRespondent);

        $previous = $this->payments->latestForSurvey($survey->id);
        if ($previous !== null && $this->canReuse($previous, $survey, $amount)) {
            return (string) $previous['redirect_url'];
        }

        $orderId = sprintf('EFS-%d-%s', $survey->id, bin2hex(random_bytes(6)));
        $paymentId = $this->payments->create(
            $survey->id,
            $orderId,
            $survey->targetRespondents,
            $survey->rewardPerRespondent,
            $amount,
        );

        try {
            $redirectUrl = $this->gateway->createCheckout(new CheckoutRequest(
                orderId: $orderId,
                grossAmount: $amount,
                items: [
                    [
                        'id' => 'respondent-rewards',
                        'name' => 'Respondent reward',
                        'price' => $survey->rewardPerRespondent,
                        'quantity' => $survey->targetRespondents,
                    ],
                    [
                        'id' => 'platform-fee',
                        'name' => 'Platform fee',
                        'price' => $this->pricing->platformFee(),
                        'quantity' => 1,
                    ],
                ],
                customerName: $owner->fullName,
                customerEmail: $owner->email,
                customerPhone: $owner->phone,
                finishUrl: $finishUrl,
            ));
        } catch (PaymentGatewayException $e) {
            $this->payments->recordGatewayState($paymentId, GatewayStatus::FAILED, 'checkout_error', null, null);

            throw $e;
        }

        $this->payments->setRedirectUrl($paymentId, $redirectUrl);

        return $redirectUrl;
    }

    /**
     * Fetches the current state of an order from the provider and applies it.
     *
     * @return string|null our payment status, or null if the order is unknown
     */
    public function refresh(string $orderId): ?string
    {
        if ($this->payments->findByOrderId($orderId) === null) {
            return null;
        }

        $status = $this->gateway->fetchStatus($orderId);

        return $status === null ? null : $this->apply($orderId, $status);
    }

    /**
     * Used when the owner lands back on our site after checkout. Webhooks
     * cannot reach a local development machine, so this pulls instead.
     */
    public function refreshLatest(int $surveyId): ?string
    {
        $payment = $this->payments->latestForSurvey($surveyId);

        if ($payment === null || $payment['status'] !== GatewayStatus::PENDING) {
            return $payment['status'] ?? null;
        }

        return $this->refresh((string) $payment['order_id']);
    }

    /**
     * Records a payment settled outside Midtrans (e.g. a bank transfer checked
     * by an administrator) and publishes the survey. CLI only.
     */
    public function recordManualPayment(int $surveyId, string $reference): void
    {
        Database::transaction($this->db, function () use ($surveyId, $reference): void {
            $survey = $this->surveys->findForUpdate($surveyId);

            if ($survey === null) {
                throw new RuleViolation(sprintf('Survey %d does not exist.', $surveyId));
            }

            if ($survey->isPaid()) {
                throw new RuleViolation(sprintf('Survey %d is already paid.', $surveyId));
            }

            if ($this->questions->count($survey->id) === 0) {
                throw new RuleViolation(sprintf('Survey %d has no questions yet.', $surveyId));
            }

            $paymentId = $this->payments->create(
                $survey->id,
                sprintf('MANUAL-%d-%s', $survey->id, bin2hex(random_bytes(4))),
                $survey->targetRespondents,
                $survey->rewardPerRespondent,
                $this->pricing->total($survey->targetRespondents, $survey->rewardPerRespondent),
            );

            $this->payments->recordGatewayState($paymentId, GatewayStatus::PAID, 'manual', 'manual', $reference);
            $this->surveys->activate($survey->id, $survey->targetRespondents, $survey->rewardPerRespondent);
        });
    }

    private function apply(string $orderId, GatewayStatus $status): string
    {
        return Database::transaction($this->db, function () use ($orderId, $status): string {
            $payment = $this->payments->findByOrderId($orderId, lock: true);
            if ($payment === null) {
                throw new RuleViolation(sprintf('Unknown order %s.', $orderId));
            }

            $paymentId = (int) $payment['id'];
            $current = (string) $payment['status'];
            $outcome = $status->outcome();

            // A settled payment is never downgraded automatically. Refunds and
            // chargebacks are flagged in the log for someone to review.
            if ($outcome === null || $current === GatewayStatus::PAID) {
                if ($current === GatewayStatus::PAID && $outcome !== GatewayStatus::PAID) {
                    error_log(sprintf('Payment %s is paid but Midtrans now reports "%s"; review manually.', $orderId, $status->transactionStatus));
                }

                $this->payments->recordGatewayState($paymentId, $current, $status->transactionStatus, $status->paymentType, $status->transactionId);

                return $current;
            }

            if ($outcome === GatewayStatus::PAID && $status->grossAmount !== (int) $payment['gross_amount']) {
                error_log(sprintf(
                    'Payment %s amount mismatch: expected %d, Midtrans reported %d. Survey not activated.',
                    $orderId,
                    (int) $payment['gross_amount'],
                    $status->grossAmount,
                ));
                $this->payments->recordGatewayState($paymentId, $current, 'amount_mismatch', $status->paymentType, $status->transactionId);

                return $current;
            }

            $this->payments->recordGatewayState($paymentId, $outcome, $status->transactionStatus, $status->paymentType, $status->transactionId);

            if ($outcome === GatewayStatus::PAID) {
                $activated = $this->surveys->activate(
                    (int) $payment['survey_id'],
                    (int) $payment['target_respondents'],
                    (int) $payment['reward_per_respondent'],
                );

                if (!$activated) {
                    error_log(sprintf('Survey %d was already active when payment %s settled; refund required.', (int) $payment['survey_id'], $orderId));
                }
            }

            return $outcome;
        });
    }

    /**
     * @param array<string, mixed> $payment
     */
    private function canReuse(array $payment, Survey $survey, int $amount): bool
    {
        return $payment['status'] === GatewayStatus::PENDING
            && is_string($payment['redirect_url'])
            && (int) $payment['gross_amount'] === $amount
            && (int) $payment['target_respondents'] === $survey->targetRespondents
            && (int) $payment['reward_per_respondent'] === $survey->rewardPerRespondent
            && strtotime((string) $payment['created_at']) > time() - self::REUSE_CHECKOUT_MINUTES * 60;
    }
}
