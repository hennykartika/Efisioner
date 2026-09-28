<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Question;
use PDO;

final class QuestionRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    /**
     * @return list<Question>
     */
    public function forSurvey(int $surveyId): array
    {
        $statement = $this->db->prepare('SELECT * FROM questions WHERE survey_id = ? ORDER BY id');
        $statement->execute([$surveyId]);

        return array_map(Question::fromRow(...), $statement->fetchAll());
    }

    /**
     * Always scoped by survey so a question id from another survey cannot be
     * edited through this one.
     */
    public function find(int $surveyId, int $questionId): ?Question
    {
        $statement = $this->db->prepare('SELECT * FROM questions WHERE id = ? AND survey_id = ?');
        $statement->execute([$questionId, $surveyId]);
        $row = $statement->fetch();

        return $row === false ? null : Question::fromRow($row);
    }

    public function count(int $surveyId): int
    {
        $statement = $this->db->prepare('SELECT COUNT(*) FROM questions WHERE survey_id = ?');
        $statement->execute([$surveyId]);

        return (int) $statement->fetchColumn();
    }

    /**
     * @param array{body: string, option_a: string, option_b: string, option_c: ?string, option_d: ?string} $data
     */
    public function create(int $surveyId, array $data): void
    {
        $this->db->prepare(
            'INSERT INTO questions (survey_id, body, option_a, option_b, option_c, option_d) VALUES (?, ?, ?, ?, ?, ?)',
        )->execute([$surveyId, $data['body'], $data['option_a'], $data['option_b'], $data['option_c'], $data['option_d']]);
    }

    /**
     * @param array{body: string, option_a: string, option_b: string, option_c: ?string, option_d: ?string} $data
     */
    public function update(int $surveyId, int $questionId, array $data): void
    {
        $this->db->prepare(
            'UPDATE questions SET body = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?
             WHERE id = ? AND survey_id = ?',
        )->execute([$data['body'], $data['option_a'], $data['option_b'], $data['option_c'], $data['option_d'], $questionId, $surveyId]);
    }

    public function delete(int $surveyId, int $questionId): void
    {
        $this->db->prepare('DELETE FROM questions WHERE id = ? AND survey_id = ?')->execute([$questionId, $surveyId]);
    }
}
