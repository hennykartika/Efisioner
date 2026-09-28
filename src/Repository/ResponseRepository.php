<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\User;
use DateTimeImmutable;
use PDO;

final class ResponseRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function exists(int $surveyId, int $respondentId): bool
    {
        $statement = $this->db->prepare('SELECT 1 FROM responses WHERE survey_id = ? AND respondent_id = ?');
        $statement->execute([$surveyId, $respondentId]);

        return $statement->fetchColumn() !== false;
    }

    /**
     * Stores the respondent's profile as it was at submission time, so later
     * profile edits do not rewrite research data.
     *
     * @param array<int, string> $choices question id => option letter
     */
    public function create(int $surveyId, User $respondent, array $choices, DateTimeImmutable $now): int
    {
        $this->db->prepare(
            'INSERT INTO responses
                (survey_id, respondent_id, respondent_name, gender, age, education, occupation, occupation_other, institution)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
        )->execute([
            $surveyId,
            $respondent->id,
            $respondent->fullName,
            $respondent->gender->value,
            $respondent->ageOn($now),
            $respondent->education->value,
            $respondent->occupation->value,
            $respondent->occupationOther,
            $respondent->institution,
        ]);

        $responseId = (int) $this->db->lastInsertId();

        $insertAnswer = $this->db->prepare('INSERT INTO answers (response_id, question_id, choice) VALUES (?, ?, ?)');
        foreach ($choices as $questionId => $choice) {
            $insertAnswer->execute([$responseId, $questionId, $choice]);
        }

        return $responseId;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function forSurvey(int $surveyId): array
    {
        $statement = $this->db->prepare('SELECT * FROM responses WHERE survey_id = ? ORDER BY submitted_at, id');
        $statement->execute([$surveyId]);

        return $statement->fetchAll();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function find(int $surveyId, int $responseId): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM responses WHERE id = ? AND survey_id = ?');
        $statement->execute([$responseId, $surveyId]);
        $row = $statement->fetch();

        return $row === false ? null : $row;
    }

    /**
     * @return array<int, string> question id => option letter
     */
    public function choices(int $responseId): array
    {
        $statement = $this->db->prepare('SELECT question_id, choice FROM answers WHERE response_id = ?');
        $statement->execute([$responseId]);

        return array_map('strval', $statement->fetchAll(PDO::FETCH_KEY_PAIR));
    }

    /**
     * Answer counts per question and option in a single query.
     *
     * @return array<int, array<string, int>> question id => [letter => count]
     */
    public function tally(int $surveyId): array
    {
        $statement = $this->db->prepare(
            'SELECT a.question_id, a.choice, COUNT(*) AS total
             FROM answers a
             JOIN questions q ON q.id = a.question_id
             WHERE q.survey_id = ?
             GROUP BY a.question_id, a.choice',
        );
        $statement->execute([$surveyId]);

        $tally = [];
        foreach ($statement->fetchAll() as $row) {
            $tally[(int) $row['question_id']][(string) $row['choice']] = (int) $row['total'];
        }

        return $tally;
    }
}
