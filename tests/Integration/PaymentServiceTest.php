<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Domain\RuleViolation;
use App\Domain\User;
use App\Payment\GatewayStatus;
use App\Payment\PaymentGatewayException;
use App\Repository\PaymentRepository;
use App\Repository\QuestionRepository;
use App\Repository\SurveyRepository;
use App\Service\PaymentService;
use App\Service\SurveyPricing;
use Tests\Support\FakeGateway;

final class PaymentServiceTest extends DatabaseTestCase
{
    private const FINISH_URL = 'https://efisioner.test/finish';

    private FakeGateway $gateway;
    private PaymentService $service;
    private SurveyRepository $surveys;
    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gateway = new FakeGateway();
        $this->surveys = new SurveyRepository($this->db);
        $this->service = new PaymentService(
            $this->db,
            $this->surveys,
            new QuestionRepository($this->db),
            new PaymentRepository($this->db),
            $this->gateway,
            new SurveyPricing(1000),
        );
        $this->owner = $this->createUser('owner');
    }

    public function testCheckoutNeedsAtLeastOneQuestion(): void
    {
        $survey = $this->createSurvey($this->owner, questions: 0);

        $this->expectException(RuleViolation::class);
        $this->service->startCheckout($survey, $this->owner, self::FINISH_URL);
    }

    public function testCheckoutChargesRewardsPlusFeeAndItemsAddUp(): void
    {
        $survey = $this->createSurvey($this->owner, target: 4, reward: 2500);

        $url = $this->service->startCheckout($survey, $this->owner, self::FINISH_URL);

        $request = $this->gateway->checkouts[0];
        $itemsTotal = array_sum(array_map(static fn (array $item): int => $item['price'] * $item['quantity'], $request->items));
        $this->assertSame(11000, $request->grossAmount);
        $this->assertSame($request->grossAmount, $itemsTotal);
        $this->assertSame('https://pay.example.test/' . $request->orderId, $url);
        $this->assertSame('pending', $this->scalar('SELECT status FROM payments WHERE order_id = ?', [$request->orderId]));
    }

    public function testPendingCheckoutIsReusedUntilPricingChanges(): void
    {
        $survey = $this->createSurvey($this->owner);

        $first = $this->service->startCheckout($survey, $this->owner, self::FINISH_URL);
        $second = $this->service->startCheckout($survey, $this->owner, self::FINISH_URL);
        $this->assertSame($first, $second);
        $this->assertCount(1, $this->gateway->checkouts);

        $this->surveys->updatePricing($survey->id, 5, 5000);
        $third = $this->service->startCheckout($this->surveys->find($survey->id), $this->owner, self::FINISH_URL);
        $this->assertNotSame($first, $third);
        $this->assertCount(2, $this->gateway->checkouts);
    }

    public function testSettlementPublishesExactlyWhatWasPaidFor(): void
    {
        $survey = $this->createSurvey($this->owner, target: 2, reward: 5000);
        $this->service->startCheckout($survey, $this->owner, self::FINISH_URL);
        $orderId = $this->gateway->lastOrderId();

        // Owner bumps the target after starting checkout but pays the old amount.
        $this->surveys->updatePricing($survey->id, 500, 5000);
        $this->gateway->report($orderId, 'settlement', 11000);

        $this->assertSame(GatewayStatus::PAID, $this->service->refresh($orderId));

        $live = $this->surveys->find($survey->id);
        $this->assertTrue($live->isPaid());
        $this->assertSame(2, $live->targetRespondents);
        $this->assertSame(2, $live->remainingSlots);
    }

    public function testAmountMismatchDoesNotPublish(): void
    {
        $survey = $this->createSurvey($this->owner, target: 2, reward: 5000);
        $this->service->startCheckout($survey, $this->owner, self::FINISH_URL);
        $orderId = $this->gateway->lastOrderId();
        $this->gateway->report($orderId, 'settlement', 1000);

        $this->assertSame(GatewayStatus::PENDING, $this->silenced(fn () => $this->service->refresh($orderId)));
        $this->assertFalse($this->surveys->find($survey->id)->isPaid());
    }

    public function testFraudChallengeAndExpiryDoNotPublish(): void
    {
        $survey = $this->createSurvey($this->owner);
        $this->service->startCheckout($survey, $this->owner, self::FINISH_URL);
        $orderId = $this->gateway->lastOrderId();

        $this->gateway->report($orderId, 'capture', 11000, 'challenge');
        $this->assertSame(GatewayStatus::PENDING, $this->service->refresh($orderId));

        $this->gateway->report($orderId, 'expire', 11000);
        $this->assertSame(GatewayStatus::FAILED, $this->service->refresh($orderId));
        $this->assertFalse($this->surveys->find($survey->id)->isPaid());
    }

    public function testPaidPaymentIsNeverDowngradedAutomatically(): void
    {
        $survey = $this->createSurvey($this->owner);
        $this->service->startCheckout($survey, $this->owner, self::FINISH_URL);
        $orderId = $this->gateway->lastOrderId();

        $this->gateway->report($orderId, 'settlement', 11000);
        $this->service->refresh($orderId);
        $this->gateway->report($orderId, 'refund', 11000);

        $this->assertSame(GatewayStatus::PAID, $this->silenced(fn () => $this->service->refresh($orderId)));
        $this->assertSame('refund', $this->scalar('SELECT gateway_status FROM payments WHERE order_id = ?', [$orderId]));
        $this->assertTrue($this->surveys->find($survey->id)->isPaid());
    }

    public function testUnknownOrderIsIgnored(): void
    {
        $this->assertNull($this->service->refresh('EFS-404-unknown'));
    }

    public function testProviderOutageMarksAttemptFailed(): void
    {
        $survey = $this->createSurvey($this->owner);
        $this->gateway->unavailable = true;

        try {
            $this->service->startCheckout($survey, $this->owner, self::FINISH_URL);
            $this->fail('Expected the gateway error to surface.');
        } catch (PaymentGatewayException) {
            $this->assertSame('failed', $this->scalar('SELECT status FROM payments WHERE survey_id = ?', [$survey->id]));
        }
    }

    public function testManualPaymentPublishesOnce(): void
    {
        $survey = $this->createSurvey($this->owner);

        $this->service->recordManualPayment($survey->id, 'BANK-TRANSFER-001');
        $this->assertTrue($this->surveys->find($survey->id)->isPaid());

        $this->expectException(RuleViolation::class);
        $this->service->recordManualPayment($survey->id, 'BANK-TRANSFER-002');
    }

    /**
     * Runs code that is expected to write to the error log without cluttering test output.
     */
    private function silenced(callable $callback): mixed
    {
        $previous = ini_set('error_log', '/dev/null');

        try {
            return $callback();
        } finally {
            ini_set('error_log', $previous === false ? '' : $previous);
        }
    }
}
