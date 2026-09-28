<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Domain\RuleViolation;
use App\Repository\QuestionRepository;
use App\Repository\ResponseRepository;
use App\Repository\RewardRepository;
use App\Repository\SurveyRepository;
use App\Service\ResponseService;
use App\Validation\ValidationException;

final class ResponseServiceTest extends DatabaseTestCase
{
    private ResponseService $service;
    private QuestionRepository $questions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->questions = new QuestionRepository($this->db);
        $this->service = new ResponseService(
            $this->db,
            new SurveyRepository($this->db),
            $this->questions,
            new ResponseRepository($this->db),
            new RewardRepository($this->db),
        );
    }

    public function testSubmissionStoresAnswersTakesSlotAndPaysReward(): void
    {
        $owner = $this->createUser('owner');
        $respondent = $this->createUser('respondent');
        $survey = $this->publish($this->createSurvey($owner, target: 3, reward: 7500, questions: 2));
        $choices = $this->choices($survey->id, ['A', 'B']);

        $this->service->submit($respondent, $survey->id, $choices);

        $this->assertSame(2, (int) $this->scalar('SELECT remaining_slots FROM surveys WHERE id = ?', [$survey->id]));
        $this->assertSame(7500, (int) $this->scalar('SELECT reward_balance FROM users WHERE id = ?', [$respondent->id]));
        $this->assertSame(1, (int) $this->scalar('SELECT COUNT(*) FROM reward_transactions WHERE user_id = ?', [$respondent->id]));
        $this->assertSame(2, (int) $this->scalar('SELECT COUNT(*) FROM answers'));
        $this->assertSame('Respondent Tester', $this->scalar('SELECT respondent_name FROM responses'));
    }

    public function testSameRespondentCannotBePaidTwice(): void
    {
        $respondent = $this->createUser('respondent');
        $survey = $this->publish($this->createSurvey($this->createUser('owner')));
        $choices = $this->choices($survey->id, ['A']);

        $this->service->submit($respondent, $survey->id, $choices);

        try {
            $this->service->submit($respondent, $survey->id, $choices);
            $this->fail('Second submission should be rejected.');
        } catch (RuleViolation $e) {
            $this->assertStringContainsString('already answered', $e->getMessage());
        }

        $this->assertSame(5000, (int) $this->scalar('SELECT reward_balance FROM users WHERE id = ?', [$respondent->id]));
    }

    public function testQuotaIsEnforced(): void
    {
        $survey = $this->publish($this->createSurvey($this->createUser('owner'), target: 1));
        $choices = $this->choices($survey->id, ['A']);

        $this->service->submit($this->createUser('first'), $survey->id, $choices);

        $this->expectException(RuleViolation::class);
        $this->service->submit($this->createUser('second'), $survey->id, $choices);
    }

    public function testOwnerCannotAnswerOwnSurvey(): void
    {
        $owner = $this->createUser('owner');
        $survey = $this->publish($this->createSurvey($owner));

        $this->expectException(RuleViolation::class);
        $this->service->submit($owner, $survey->id, $this->choices($survey->id, ['A']));
    }

    public function testUnpaidSurveyCannotBeAnswered(): void
    {
        $survey = $this->createSurvey($this->createUser('owner'));

        $this->expectException(RuleViolation::class);
        $this->service->submit($this->createUser('respondent'), $survey->id, $this->choices($survey->id, ['A']));
    }

    public function testEveryQuestionNeedsOneOfItsOwnOptions(): void
    {
        $survey = $this->publish($this->createSurvey($this->createUser('owner'), questions: 2));
        $respondent = $this->createUser('respondent');
        [$first, $second] = array_keys($this->choices($survey->id, ['A', 'A']));

        foreach ([[$first => 'A'], [$first => 'A', $second => 'C'], [$first => 'A', $second => '']] as $choices) {
            try {
                $this->service->submit($respondent, $survey->id, $choices);
                $this->fail('Incomplete or invalid answers should be rejected.');
            } catch (ValidationException $e) {
                $this->assertArrayHasKey('q' . $second, $e->errors);
            }
        }

        $this->assertSame(0, (int) $this->scalar('SELECT COUNT(*) FROM responses'));
        $this->assertSame(0, (int) $this->scalar('SELECT reward_balance FROM users WHERE id = ?', [$respondent->id]));
    }

    public function testAnswersForUnknownQuestionsAreRejected(): void
    {
        $survey = $this->publish($this->createSurvey($this->createUser('owner')));
        $choices = $this->choices($survey->id, ['A']) + [999999 => 'A'];

        $this->expectException(RuleViolation::class);
        $this->service->submit($this->createUser('respondent'), $survey->id, $choices);
    }

    /**
     * @param list<string> $letters one per question, in order
     * @return array<int, string>
     */
    private function choices(int $surveyId, array $letters): array
    {
        $choices = [];
        foreach ($this->questions->forSurvey($surveyId) as $index => $question) {
            $choices[$question->id] = $letters[$index];
        }

        return $choices;
    }
}
