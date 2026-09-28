<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Question;
use App\Domain\RuleViolation;
use App\Domain\User;
use App\Repository\QuestionRepository;
use App\Repository\ResponseRepository;
use App\Repository\RewardRepository;
use App\Repository\SurveyRepository;
use App\Support\Database;
use App\Validation\ValidationException;
use DateTimeImmutable;
use PDO;
use PDOException;

final class ResponseService
{
    public function __construct(
        private readonly PDO $db,
        private readonly SurveyRepository $surveys,
        private readonly QuestionRepository $questions,
        private readonly ResponseRepository $responses,
        private readonly RewardRepository $rewards,
    ) {
    }

    /**
     * Saves a response, takes one respondent slot and credits the reward as a
     * single unit of work. The survey row is locked so two respondents cannot
     * both claim the last slot.
     *
     * @param array<int|string, string> $choices question id => option letter, as posted
     */
    public function submit(User $respondent, int $surveyId, array $choices): void
    {
        try {
            Database::transaction($this->db, function () use ($respondent, $surveyId, $choices): void {
                $survey = $this->surveys->findForUpdate($surveyId);

                if ($survey === null || !$survey->acceptsResponses()) {
                    throw new RuleViolation('This survey is no longer accepting responses.');
                }

                if ($survey->isOwnedBy($respondent)) {
                    throw new RuleViolation('You cannot answer your own survey.');
                }

                if ($this->responses->exists($surveyId, $respondent->id)) {
                    throw new RuleViolation('You have already answered this survey.');
                }

                $answers = $this->validateChoices($this->questions->forSurvey($surveyId), $choices);

                $this->responses->create($surveyId, $respondent, $answers, new DateTimeImmutable());

                if (!$this->surveys->takeSlot($surveyId)) {
                    throw new RuleViolation('This survey is no longer accepting responses.');
                }

                $this->rewards->credit(
                    $respondent->id,
                    $surveyId,
                    mb_substr('Reward: ' . $survey->title, 0, 255),
                    $survey->rewardPerRespondent,
                );
            });
        } catch (PDOException $e) {
            if (Database::isDuplicateKey($e)) {
                throw new RuleViolation('You have already answered this survey.');
            }

            throw $e;
        }
    }

    /**
     * Every question must be answered with one of its own options, and nothing
     * else may be submitted.
     *
     * @param list<Question> $questions
     * @param array<int|string, string> $choices
     * @return array<int, string>
     */
    private function validateChoices(array $questions, array $choices): array
    {
        if ($questions === []) {
            throw new RuleViolation('This survey has no questions yet.');
        }

        $answers = [];
        $errors = [];

        foreach ($questions as $question) {
            $choice = $choices[$question->id] ?? '';

            if (!$question->hasOption($choice)) {
                $errors['q' . $question->id] = 'Please choose an answer.';
                continue;
            }

            $answers[$question->id] = $choice;
        }

        if (count($choices) !== count($answers) && $errors === []) {
            throw new RuleViolation('The survey changed while you were answering it. Please reload the page and try again.');
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        return $answers;
    }
}
