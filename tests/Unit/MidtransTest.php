<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Payment\GatewayStatus;
use App\Payment\MidtransGateway;
use App\Payment\PaymentGatewayException;
use PHPUnit\Framework\TestCase;

final class MidtransTest extends TestCase
{
    private const SERVER_KEY = 'SB-Mid-server-unit-test';

    public function testAcceptsCorrectlySignedNotification(): void
    {
        $gateway = new MidtransGateway(self::SERVER_KEY, false);

        $this->assertTrue($gateway->isAuthentic($this->signed(['order_id' => 'EFS-1-a', 'status_code' => '200', 'gross_amount' => '11000.00'])));
    }

    public function testRejectsTamperedAmountOrMissingFields(): void
    {
        $gateway = new MidtransGateway(self::SERVER_KEY, false);

        $payload = $this->signed(['order_id' => 'EFS-1-a', 'status_code' => '200', 'gross_amount' => '11000.00']);
        $payload['gross_amount'] = '1000.00';
        $this->assertFalse($gateway->isAuthentic($payload));

        $this->assertFalse($gateway->isAuthentic(['order_id' => 'EFS-1-a']));
        $this->assertFalse($gateway->isAuthentic([
            'order_id' => ['array'],
            'status_code' => '200',
            'gross_amount' => '1.00',
            'signature_key' => 'x',
        ]));
    }

    public function testRejectsEverythingWhenServerKeyIsMissing(): void
    {
        $unsigned = ['order_id' => 'o', 'status_code' => '200', 'gross_amount' => '1.00'];
        $unsigned['signature_key'] = hash('sha512', 'o2001.00');

        $this->assertFalse((new MidtransGateway('', false))->isAuthentic($unsigned));
    }

    public function testStatusMapping(): void
    {
        $cases = [
            ['settlement', null, GatewayStatus::PAID],
            ['capture', 'accept', GatewayStatus::PAID],
            ['capture', 'challenge', GatewayStatus::PENDING],
            ['capture', null, GatewayStatus::PENDING],
            ['pending', null, GatewayStatus::PENDING],
            ['expire', null, GatewayStatus::FAILED],
            ['cancel', null, GatewayStatus::FAILED],
            ['deny', null, GatewayStatus::FAILED],
            ['refund', null, null],
            ['chargeback', null, null],
        ];

        foreach ($cases as [$transactionStatus, $fraudStatus, $expected]) {
            $status = GatewayStatus::fromMidtrans([
                'order_id' => 'o',
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
                'gross_amount' => '5000.00',
            ]);

            $this->assertSame($expected, $status->outcome(), $transactionStatus . '/' . ($fraudStatus ?? 'none'));
        }
    }

    public function testParsesWholeRupiahAmountsOnly(): void
    {
        $this->assertSame(150000, GatewayStatus::fromMidtrans(['gross_amount' => '150000.00'])->grossAmount);
        $this->assertSame(150000, GatewayStatus::fromMidtrans(['gross_amount' => '150000'])->grossAmount);

        $this->expectException(PaymentGatewayException::class);
        GatewayStatus::fromMidtrans(['gross_amount' => '150000.50']);
    }

    /**
     * @param array<string, string> $payload
     * @return array<string, string>
     */
    private function signed(array $payload): array
    {
        $payload['signature_key'] = hash(
            'sha512',
            $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . self::SERVER_KEY,
        );

        return $payload;
    }
}
