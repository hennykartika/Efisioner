<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain\Survey;
use App\Http\Request;
use App\Http\Response;
use App\Http\Session;
use App\Repository\PaymentRepository;
use App\Repository\QuestionRepository;
use App\Repository\SurveyRepository;
use App\Security\Auth;
use App\Security\SurveyPolicy;
use App\Service\SurveyPricing;
use App\Validation\Validator;
use App\View\View;

/**
 * The researcher side: create surveys and manage drafts.
 */
final class SurveyController extends Controller
{
    private const FIELDS = ['title', 'description', 'target_respondents', 'reward_per_respondent'];

    public function __construct(
        View $view,
        Auth $auth,
        Session $session,
        private readonly SurveyRepository $surveys,
        private readonly QuestionRepository $questions,
        private readonly PaymentRepository $payments,
        private readonly SurveyPricing $pricing,
        private readonly SurveyPolicy $policy,
    ) {
        parent::__construct($view, $auth, $session);
    }

    public function index(): Response
    {
        $user = $this->requireUser();

        return $this->render('surveys/index', ['items' => $this->surveys->listByOwner($user->id)]);
    }

    public function create(): Response
    {
        $this->requireUser();

        return $this->render('surveys/create', [
            'old' => ['target_respondents' => '', 'reward_per_respondent' => ''],
            'errors' => [],
            'platformFee' => $this->pricing->platformFee(),
        ]);
    }

    public function store(Request $request): Response
    {
        $user = $this->requireUser();

        $validator = new Validator($request->only(self::FIELDS));
        $this->validateContent($validator);
        $this->validatePricing($validator);

        if (!$validator->passes()) {
            return $this->render('surveys/create', [
                'old' => $validator->data(),
                'errors' => $validator->errors(),
                'platformFee' => $this->pricing->platformFee(),
            ], 422);
        }

        $surveyId = $this->surveys->create(
            $user->id,
            $validator->value('title'),
            $validator->value('description'),
            (int) $validator->value('target_respondents'),
            (int) $validator->value('reward_per_respondent'),
        );

        return $this->redirectWith("/my-surveys/{$surveyId}/edit", 'success', 'Survey created. Add your questions, then pay to publish it.');
    }

    public function edit(Request $request, int $surveyId): Response
    {
        $survey = $this->policy->owned($this->requireUser(), $surveyId);

        return $this->renderEdit($survey, [], []);
    }

    public function update(Request $request, int $surveyId): Response
    {
        $survey = $this->policy->owned($this->requireUser(), $surveyId);

        if (!$survey->canChangeContent() && !$survey->canChangePricing()) {
            return $this->redirectWith("/my-surveys/{$surveyId}/edit", 'danger', 'This survey can no longer be edited.');
        }

        $validator = new Validator($request->only(self::FIELDS));
        if ($survey->canChangeContent()) {
            $this->validateContent($validator);
        }
        if ($survey->canChangePricing()) {
            $this->validatePricing($validator);
        }

        if (!$validator->passes()) {
            $editable = [
                ...($survey->canChangeContent() ? ['title', 'description'] : []),
                ...($survey->canChangePricing() ? ['target_respondents', 'reward_per_respondent'] : []),
            ];

            return $this->renderEdit(
                $survey,
                array_intersect_key($validator->data(), array_flip($editable)),
                $validator->errors(),
                422,
            );
        }

        if ($survey->canChangeContent()) {
            $this->surveys->updateContent($survey->id, $validator->value('title'), $validator->value('description'));
        }
        if ($survey->canChangePricing()) {
            $this->surveys->updatePricing(
                $survey->id,
                (int) $validator->value('target_respondents'),
                (int) $validator->value('reward_per_respondent'),
            );
        }

        return $this->redirectWith("/my-surveys/{$surveyId}/edit", 'success', 'Survey details saved.');
    }

    /**
     * @param array<string, string> $old
     * @param array<string, string> $errors
     */
    private function renderEdit(Survey $survey, array $old, array $errors, int $status = 200): Response
    {
        $defaults = [
            'title' => $survey->title,
            'description' => $survey->description,
            'target_respondents' => (string) $survey->targetRespondents,
            'reward_per_respondent' => (string) $survey->rewardPerRespondent,
        ];

        return $this->render('surveys/edit', [
            'survey' => $survey,
            'old' => [...$defaults, ...$old],
            'errors' => $errors,
            'questions' => $this->questions->forSurvey($survey->id),
            'payment' => $this->payments->latestForSurvey($survey->id),
            'platformFee' => $this->pricing->platformFee(),
            'total' => $this->pricing->total($survey->targetRespondents, $survey->rewardPerRespondent),
        ], $status);
    }

    private function validateContent(Validator $validator): void
    {
        $validator
            ->required('title', 'Title')
            ->length('title', 'Title', 5, 150)
            ->required('description', 'Description')
            ->length('description', 'Description', 10, 2000);
    }

    private function validatePricing(Validator $validator): void
    {
        $validator
            ->required('target_respondents', 'Target respondents')
            ->integer('target_respondents', 'Target respondents', SurveyPricing::MIN_RESPONDENTS, SurveyPricing::MAX_RESPONDENTS)
            ->required('reward_per_respondent', 'Reward per respondent')
            ->integer('reward_per_respondent', 'Reward per respondent', SurveyPricing::MIN_REWARD, SurveyPricing::MAX_REWARD);
    }
}
