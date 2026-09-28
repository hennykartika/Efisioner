<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Survey;
use PDO;

final class SurveyRepository
{
    /**
     * Surveys a respondent can answer right now. Kept in one place because the
     * home page and the survey list must agree on it.
     */
    private const OPEN_CONDITION = 's.paid_at IS NOT NULL
        AND s.closed_at IS NULL
        AND s.remaining_slots > 0
        AND EXISTS (SELECT 1 FROM questions q WHERE q.survey_id = s.id)';

    public function __construct(private readonly PDO $db)
    {
    }

    public function find(int $id): ?Survey
    {
        $statement = $this->db->prepare('SELECT * FROM surveys WHERE id = ?');
        $statement->execute([$id]);
        $row = $statement->fetch();

        return $row === false ? null : Survey::fromRow($row);
    }

    /**
     * Locks the row until the surrounding transaction ends.
     */
    public function findForUpdate(int $id): ?Survey
    {
        $statement = $this->db->prepare('SELECT * FROM surveys WHERE id = ? FOR UPDATE');
        $statement->execute([$id]);
        $row = $statement->fetch();

        return $row === false ? null : Survey::fromRow($row);
    }

    public function create(int $ownerId, string $title, string $description, int $target, int $reward): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO surveys (owner_id, title, description, target_respondents, reward_per_respondent, remaining_slots)
             VALUES (?, ?, ?, ?, ?, ?)',
        );
        $statement->execute([$ownerId, $title, $description, $target, $reward, $target]);

        return (int) $this->db->lastInsertId();
    }

    public function updateContent(int $id, string $title, string $description): void
    {
        $this->db->prepare('UPDATE surveys SET title = ?, description = ? WHERE id = ?')
            ->execute([$title, $description, $id]);
    }

    public function updatePricing(int $id, int $target, int $reward): void
    {
        $statement = $this->db->prepare(
            'UPDATE surveys
             SET target_respondents = ?, reward_per_respondent = ?, remaining_slots = ?
             WHERE id = ? AND paid_at IS NULL',
        );
        $statement->execute([$target, $reward, $target, $id]);
    }

    /**
     * Publishes the survey with exactly what was paid for, even if the owner
     * edited the draft after starting checkout.
     */
    public function activate(int $id, int $target, int $reward): bool
    {
        $statement = $this->db->prepare(
            'UPDATE surveys
             SET target_respondents = ?, reward_per_respondent = ?, remaining_slots = ?, paid_at = NOW()
             WHERE id = ? AND paid_at IS NULL',
        );
        $statement->execute([$target, $reward, $target, $id]);

        return $statement->rowCount() > 0;
    }

    public function takeSlot(int $id): bool
    {
        $statement = $this->db->prepare(
            'UPDATE surveys SET remaining_slots = remaining_slots - 1
             WHERE id = ? AND remaining_slots > 0 AND paid_at IS NOT NULL AND closed_at IS NULL',
        );
        $statement->execute([$id]);

        return $statement->rowCount() === 1;
    }

    public function close(int $id): void
    {
        $this->db->prepare('UPDATE surveys SET closed_at = NOW() WHERE id = ? AND closed_at IS NULL')->execute([$id]);
    }

    /**
     * Only unpaid drafts without any checkout attempt can be removed; paid
     * surveys keep their financial trail and are closed instead.
     */
    public function deleteDraft(int $id): bool
    {
        $statement = $this->db->prepare(
            'DELETE FROM surveys
             WHERE id = ? AND paid_at IS NULL
               AND NOT EXISTS (SELECT 1 FROM payments p WHERE p.survey_id = surveys.id)',
        );
        $statement->execute([$id]);

        return $statement->rowCount() > 0;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listOpen(?int $respondentId, int $limit = 50): array
    {
        $statement = $this->db->prepare(
            'SELECT s.id, s.title, s.description, s.reward_per_respondent, s.remaining_slots, u.full_name AS owner_name
             FROM surveys s
             JOIN users u ON u.id = s.owner_id
             WHERE ' . self::OPEN_CONDITION . '
               AND s.owner_id <> :viewer
               AND NOT EXISTS (SELECT 1 FROM responses r WHERE r.survey_id = s.id AND r.respondent_id = :viewer_again)
             ORDER BY s.paid_at DESC
             LIMIT :limit',
        );
        $statement->bindValue('viewer', $respondentId ?? 0, PDO::PARAM_INT);
        $statement->bindValue('viewer_again', $respondentId ?? 0, PDO::PARAM_INT);
        $statement->bindValue('limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * @return list<array{survey: Survey, question_count: int}>
     */
    public function listByOwner(int $ownerId): array
    {
        $statement = $this->db->prepare(
            'SELECT s.*, (SELECT COUNT(*) FROM questions q WHERE q.survey_id = s.id) AS question_count
             FROM surveys s
             WHERE s.owner_id = ?
             ORDER BY s.created_at DESC, s.id DESC',
        );
        $statement->execute([$ownerId]);

        return array_map(
            static fn (array $row): array => ['survey' => Survey::fromRow($row), 'question_count' => (int) $row['question_count']],
            $statement->fetchAll(),
        );
    }

    /**
     * @return list<array{survey: Survey, owner_username: string}>
     */
    public function paginate(int $page, int $perPage): array
    {
        $statement = $this->db->prepare(
            'SELECT s.*, u.username AS owner_username
             FROM surveys s
             JOIN users u ON u.id = s.owner_id
             ORDER BY s.id DESC
             LIMIT :limit OFFSET :offset',
        );
        $statement->bindValue('limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue('offset', ($page - 1) * $perPage, PDO::PARAM_INT);
        $statement->execute();

        return array_map(
            static fn (array $row): array => ['survey' => Survey::fromRow($row), 'owner_username' => (string) $row['owner_username']],
            $statement->fetchAll(),
        );
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM surveys')->fetchColumn();
    }
}
