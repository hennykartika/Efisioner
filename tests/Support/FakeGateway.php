<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Payment\CheckoutRequest;
use App\Payment\GatewayStatus;
use App\Payment\PaymentGateway;
use App\Payment\PaymentGatewayException;

/**
 * In-memory stand-in for Midtrans. Tests decide what the "provider" reports.
 */
final class FakeGateway implements PaymentGateway
{
    /** @var list<CheckoutRequest> */
    public array $checkouts = [];

    public bool $unavailable = false;

    /** @var array<string, GatewayStatus> */
    private array $statuses = [];

    public function createCheckout(CheckoutRequest $request): string
    {
        if ($this->unavailable) {
            throw new PaymentGatewayException('Provider unavailable.');
        }

        $this->checkouts[] = $request;

        return 'https://pay.example.test/' . $request->orderId;
    }

    public function fetchStatus(string $orderId): ?GatewayStatus
    {
        return $this->statuses[$orderId] ?? null;
    }

    public function isAuthentic(array $payload): bool
    {
        return true;
    }

    public function report(string $orderId, string $transactionStatus, int $amount, ?string $fraudStatus = null): void
    {
        $this->statuses[$orderId] = new GatewayStatus($orderId, $transactionStatus, $fraudStatus, $amount, 'bank_transfer', 'txn-' . $orderId);
    }

    public function lastOrderId(): string
    {
        return $this->checkouts[array_key_last($this->checkouts)]->orderId;
    }
}
