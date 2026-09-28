<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class RewardRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    /**
     * Writes the ledger entry and updates the cached balance. Call it inside a
     * transaction so the two can never drift apart.
     */
    public function credit(int $userId, ?int $surveyId, string $description, int $amount): void
    {
        $this->db->prepare(
            'INSERT INTO reward_transactions (user_id, survey_id, description, amount) VALUES (?, ?, ?, ?)',
        )->execute([$userId, $surveyId, $description, $amount]);

        $this->db->prepare('UPDATE users SET reward_balance = reward_balance + ? WHERE id = ?')
            ->execute([$amount, $userId]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function history(int $userId, int $limit = 100): array
    {
        $statement = $this->db->prepare(
            'SELECT description, amount, created_at
             FROM reward_transactions
             WHERE user_id = :user
             ORDER BY created_at DESC, id DESC
             LIMIT :limit',
        );
        $statement->bindValue('user', $userId, PDO::PARAM_INT);
        $statement->bindValue('limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }
}
