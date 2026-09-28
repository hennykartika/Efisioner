<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain\RuleViolation;
use App\Http\Request;
use App\Http\Response;
use App\Http\Session;
use App\Payment\GatewayStatus;
use App\Payment\PaymentGateway;
use App\Payment\PaymentGatewayException;
use App\Security\Auth;
use App\Security\SurveyPolicy;
use App\Service\PaymentService;
use App\Support\Url;
use App\View\View;

final class PaymentController extends Controller
{
    public function __construct(
        View $view,
        Auth $auth,
        Session $session,
        private readonly PaymentService $payments,
        private readonly PaymentGateway $gateway,
        private readonly SurveyPolicy $policy,
    ) {
        parent::__construct($view, $auth, $session);
    }

    public function checkout(Request $request, int $surveyId): Response
    {
        $user = $this->requireUser();
        $survey = $this->policy->owned($user, $surveyId);

        try {
            $redirectUrl = $this->payments->startCheckout(
                $survey,
                $user,
                Url::absolute("/my-surveys/{$survey->id}/payment/finish"),
            );
        } catch (RuleViolation $e) {
            return $this->redirectWith("/my-surveys/{$survey->id}/edit", 'danger', $e->getMessage());
        } catch (PaymentGatewayException $e) {
            error_log('Checkout failed: ' . $e->getMessage());

            return $this->redirectWith(
                "/my-surveys/{$survey->id}/edit",
                'danger',
                'The payment provider is not available right now. Please try again in a few minutes.',
            );
        }

        return Response::redirect($redirectUrl);
    }

    /**
     * Midtrans sends the payer back here. Query parameters are ignored because
     * anyone can type them; the status is fetched from Midtrans directly.
     */
    public function finish(Request $request, int $surveyId): Response
    {
        $survey = $this->policy->owned($this->requireUser(), $surveyId);

        try {
            $status = $this->payments->refreshLatest($survey->id);
        } catch (PaymentGatewayException $e) {
            error_log('Payment status check failed: ' . $e->getMessage());
            $status = null;
        }

        return match ($status) {
            GatewayStatus::PAID => $this->redirectWith('/my-surveys', 'success', 'Payment received. Your survey is now live.'),
            GatewayStatus::FAILED => $this->redirectWith("/my-surveys/{$survey->id}/edit", 'danger', 'The payment was not completed. You can try again.'),
            default => $this->redirectWith("/my-surveys/{$survey->id}/edit", 'info', 'Payment is still being confirmed by the provider. Refresh this page in a moment.'),
        };
    }

    /**
     * Server-to-server notification from Midtrans. Exempt from CSRF; trust
     * comes from the signature and from re-reading the status via the API.
     */
    public function notification(Request $request): Response
    {
        $payload = json_decode($request->body(), true);

        if (!is_array($payload) || !$this->gateway->isAuthentic($payload)) {
            return Response::json(['message' => 'Invalid signature.'], 403);
        }

        try {
            $status = $this->payments->refresh((string) $payload['order_id']);
        } catch (PaymentGatewayException $e) {
            error_log('Notification handling failed: ' . $e->getMessage());

            // Non-2xx makes Midtrans retry later.
            return Response::json(['message' => 'Temporarily unavailable.'], 503);
        }

        if ($status === null) {
            return Response::json(['message' => 'Unknown order.'], 404);
        }

        return Response::json(['message' => 'OK']);
    }
}
