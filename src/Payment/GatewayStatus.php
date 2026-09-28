<?php

declare(strict_types=1);

namespace App\Payment;

final class GatewayStatus
{
    public const PAID = 'paid';
    public const PENDING = 'pending';
    public const FAILED = 'failed';

    public function __construct(
        public readonly string $orderId,
        public readonly string $transactionStatus,
        public readonly ?string $fraudStatus,
        public readonly int $grossAmount,
        public readonly ?string $paymentType,
        public readonly ?string $transactionId,
    ) {
    }

    /**
     * @param array<string, mixed> $data Midtrans status or notification body
     */
    public static function fromMidtrans(array $data): self
    {
        return new self(
            orderId: (string) ($data['order_id'] ?? ''),
            transactionStatus: (string) ($data['transaction_status'] ?? ''),
            fraudStatus: isset($data['fraud_status']) ? (string) $data['fraud_status'] : null,
            grossAmount: self::parseAmount((string) ($data['gross_amount'] ?? '')),
            paymentType: isset($data['payment_type']) ? (string) $data['payment_type'] : null,
            transactionId: isset($data['transaction_id']) ? (string) $data['transaction_id'] : null,
        );
    }

    /**
     * Maps the provider state to ours. Null means "leave it alone": refunds and
     * chargebacks are rare enough to be handled by a person, not automatically.
     */
    public function outcome(): ?string
    {
        return match ($this->transactionStatus) {
            'settlement' => self::PAID,
            'capture' => match ($this->fraudStatus) {
                'accept' => self::PAID,
                'deny' => self::FAILED,
                default => self::PENDING,
            },
            'pending' => self::PENDING,
            'deny', 'cancel', 'expire', 'failure' => self::FAILED,
            default => null,
        };
    }

    /**
     * IDR has no minor unit; Midtrans still sends "150000.00".
     */
    private static function parseAmount(string $value): int
    {
        if (preg_match('/^(\d{1,15})(?:\.0{1,2})?$/', $value, $matches) !== 1) {
            throw new PaymentGatewayException(sprintf('Unexpected gross_amount "%s".', $value));
        }

        return (int) $matches[1];
    }
}
