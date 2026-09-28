<?php

declare(strict_types=1);

namespace App\Payment;

/**
 * Midtrans Snap (redirect flow) and the Core API status endpoint.
 * https://docs.midtrans.com/reference/snap-api
 */
final class MidtransGateway implements PaymentGateway
{
    private const CONNECT_TIMEOUT = 5;
    private const TIMEOUT = 20;

    public function __construct(
        private readonly string $serverKey,
        private readonly bool $production,
    ) {
    }

    public function createCheckout(CheckoutRequest $request): string
    {
        $payload = [
            'transaction_details' => [
                'order_id' => $request->orderId,
                'gross_amount' => $request->grossAmount,
            ],
            'item_details' => array_map(static fn (array $item): array => [
                'id' => $item['id'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'name' => mb_substr($item['name'], 0, 50),
            ], $request->items),
            'customer_details' => array_filter([
                'first_name' => mb_substr($request->customerName, 0, 255),
                'email' => $request->customerEmail,
                'phone' => $request->customerPhone,
            ]),
            'callbacks' => ['finish' => $request->finishUrl],
            'expiry' => ['unit' => 'hours', 'duration' => 24],
        ];

        [$status, $body] = $this->send('POST', $this->snapHost() . '/snap/v1/transactions', $payload);

        if ($status !== 201 || !isset($body['redirect_url']) || !is_string($body['redirect_url'])) {
            throw new PaymentGatewayException(sprintf(
                'Snap checkout failed with HTTP %d: %s',
                $status,
                json_encode($body['error_messages'] ?? $body),
            ));
        }

        return $body['redirect_url'];
    }

    public function fetchStatus(string $orderId): ?GatewayStatus
    {
        [$status, $body] = $this->send('GET', $this->apiHost() . '/v2/' . rawurlencode($orderId) . '/status');

        if ($status === 404 || ($body['status_code'] ?? null) === '404') {
            return null;
        }

        if ($status !== 200 || !isset($body['transaction_status'])) {
            throw new PaymentGatewayException(sprintf('Status check for %s failed with HTTP %d.', $orderId, $status));
        }

        return GatewayStatus::fromMidtrans($body);
    }

    public function isAuthentic(array $payload): bool
    {
        foreach (['order_id', 'status_code', 'gross_amount', 'signature_key'] as $field) {
            if (!isset($payload[$field]) || !is_string($payload[$field])) {
                return false;
            }
        }

        if ($this->serverKey === '') {
            return false;
        }

        $expected = hash(
            'sha512',
            $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . $this->serverKey,
        );

        return hash_equals($expected, $payload['signature_key']);
    }

    /**
     * @param array<string, mixed>|null $payload
     * @return array{0: int, 1: array<string, mixed>}
     */
    private function send(string $method, string $url, ?array $payload = null): array
    {
        if ($this->serverKey === '') {
            throw new PaymentGatewayException('MIDTRANS_SERVER_KEY is not configured.');
        }

        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => self::CONNECT_TIMEOUT,
            CURLOPT_TIMEOUT => self::TIMEOUT,
            CURLOPT_USERPWD => $this->serverKey . ':',
            CURLOPT_HTTPHEADER => ['Accept: application/json', 'Content-Type: application/json'],
        ]);

        if ($payload !== null) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload, JSON_THROW_ON_ERROR));
        }

        $raw = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $error = curl_error($curl);

        if ($raw === false) {
            throw new PaymentGatewayException('Could not reach Midtrans: ' . $error);
        }

        $body = json_decode((string) $raw, true);

        return [$status, is_array($body) ? $body : []];
    }

    private function snapHost(): string
    {
        return $this->production ? 'https://app.midtrans.com' : 'https://app.sandbox.midtrans.com';
    }

    private function apiHost(): string
    {
        return $this->production ? 'https://api.midtrans.com' : 'https://api.sandbox.midtrans.com';
    }
}
