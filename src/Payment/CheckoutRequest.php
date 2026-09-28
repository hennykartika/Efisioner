<?php

declare(strict_types=1);

namespace App\Payment;

final class CheckoutRequest
{
    /**
     * @param list<array{id: string, name: string, price: int, quantity: int}> $items
     */
    public function __construct(
        public readonly string $orderId,
        public readonly int $grossAmount,
        public readonly array $items,
        public readonly string $customerName,
        public readonly string $customerEmail,
        public readonly ?string $customerPhone,
        public readonly string $finishUrl,
    ) {
    }
}
