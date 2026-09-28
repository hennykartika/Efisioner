<?php

declare(strict_types=1);

namespace App\Payment;

interface PaymentGateway
{
    /**
     * Creates a hosted checkout session and returns the URL to send the payer to.
     *
     * @throws PaymentGatewayException
     */
    public function createCheckout(CheckoutRequest $request): string;

    /**
     * Asks the provider for the current state of an order. Returns null when
     * the provider does not know the order.
     *
     * @throws PaymentGatewayException
     */
    public function fetchStatus(string $orderId): ?GatewayStatus;

    /**
     * Checks that a notification payload was signed with our server key.
     *
     * @param array<string, mixed> $payload
     */
    public function isAuthentic(array $payload): bool;
}
