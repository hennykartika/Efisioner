<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class PaymentRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function create(int $surveyId, string $orderId, int $target, int $reward, int $grossAmount): int
    {
        $this->db->prepare(
            'INSERT INTO payments (survey_id, order_id, target_respondents, reward_per_respondent, gross_amount)
             VALUES (?, ?, ?, ?, ?)',
        )->execute([$surveyId, $orderId, $target, $reward, $grossAmount]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findByOrderId(string $orderId, bool $lock = false): ?array
    {
        $statement = $this->db->prepare(
            'SELECT * FROM payments WHERE order_id = ?' . ($lock ? ' FOR UPDATE' : ''),
        );
        $statement->execute([$orderId]);
        $row = $statement->fetch();

        return $row === false ? null : $row;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function latestForSurvey(int $surveyId): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM payments WHERE survey_id = ? ORDER BY id DESC LIMIT 1');
        $statement->execute([$surveyId]);
        $row = $statement->fetch();

        return $row === false ? null : $row;
    }

    public function setRedirectUrl(int $id, string $url): void
    {
        $this->db->prepare('UPDATE payments SET redirect_url = ? WHERE id = ?')->execute([$url, $id]);
    }

    public function recordGatewayState(
        int $id,
        string $status,
        ?string $gatewayStatus,
        ?string $paymentType,
        ?string $transactionId,
    ): void {
        $this->db->prepare(
            'UPDATE payments
             SET status = ?,
                 gateway_status = ?,
                 payment_type = COALESCE(?, payment_type),
                 gateway_transaction_id = COALESCE(?, gateway_transaction_id),
                 paid_at = IF(? = \'paid\' AND paid_at IS NULL, NOW(), paid_at)
             WHERE id = ?',
        )->execute([$status, $gatewayStatus, $paymentType, $transactionId, $status, $id]);
    }
}
