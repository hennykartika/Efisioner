<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain\RuleViolation;
use App\Domain\Survey;
use App\Domain\User;
use App\Http\HttpException;
use App\Http\Request;
use App\Http\Response;
use App\Http\Session;
use App\Repository\QuestionRepository;
use App\Repository\ResponseRepository;
use App\Repository\SurveyRepository;
use App\Security\Auth;
use App\Service\ResponseService;
use App\Validation\ValidationException;
use App\View\View;
use DateTimeImmutable;

/**
 * The respondent side: find open surveys and answer them.
 */
final class ParticipationController extends Controller
{
    public function __construct(
        View $view,
        Auth $auth,
        Session $session,
        private readonly SurveyRepository $surveys,
        private readonly QuestionRepository $questions,
        private readonly ResponseRepository $responses,
        private readonly ResponseService $responseService,
    ) {
        parent::__construct($view, $auth, $session);
    }

    public function index(): Response
    {
        $user = $this->requireUser();

        return $this->render('participation/index', ['surveys' => $this->surveys->listOpen($user->id)]);
    }

    public function show(Request $request, int $surveyId): Response
    {
        $user = $this->requireUser();
        $survey = $this->answerableSurvey($user, $surveyId);

        if ($survey instanceof Response) {
            return $survey;
        }

        return $this->renderForm($user, $survey, [], []);
    }

    public function submit(Request $request, int $surveyId): Response
    {
        $user = $this->requireUser();
        $choices = $request->inputMap('answers');

        try {
            $this->responseService->submit($user, $surveyId, $choices);
        } catch (ValidationException $e) {
            $survey = $this->surveys->find($surveyId) ?? throw HttpException::notFound();

            return $this->renderForm($user, $survey, $choices, $e->errors, 422);
        } catch (RuleViolation $e) {
            return $this->redirectWith('/surveys', 'danger', $e->getMessage());
        }

        return $this->redirectWith('/rewards', 'success', 'Thank you! Your answers were saved and the reward has been added to your balance.');
    }

    private function answerableSurvey(User $user, int $surveyId): Survey|Response
    {
        $survey = $this->surveys->find($surveyId);

        if ($survey === null || !$survey->acceptsResponses()) {
            throw HttpException::notFound();
        }

        if ($survey->isOwnedBy($user)) {
            return $this->redirectWith('/my-surveys', 'info', 'You cannot answer your own survey.');
        }

        if ($this->responses->exists($survey->id, $user->id)) {
            return $this->redirectWith('/surveys', 'info', 'You have already answered this survey.');
        }

        return $survey;
    }

    /**
     * @param array<int|string, string> $choices
     * @param array<string, string> $errors
     */
    private function renderForm(User $user, Survey $survey, array $choices, array $errors, int $status = 200): Response
    {
        return $this->render('participation/show', [
            'survey' => $survey,
            'questions' => $this->questions->forSurvey($survey->id),
            'respondent' => $user,
            'age' => $user->ageOn(new DateTimeImmutable()),
            'choices' => $choices,
            'errors' => $errors,
        ], $status);
    }
}
