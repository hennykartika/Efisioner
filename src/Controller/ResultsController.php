<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain\Question;
use App\Domain\Survey;
use App\Domain\User;
use App\Http\HttpException;
use App\Http\Request;
use App\Http\Response;
use App\Http\Session;
use App\Repository\QuestionRepository;
use App\Repository\ResponseRepository;
use App\Security\Auth;
use App\Security\SurveyPolicy;
use App\View\View;

/**
 * Results are shared by the survey owner and administrators; both reach the
 * same pages through their own URL prefix.
 */
final class ResultsController extends Controller
{
    public function __construct(
        View $view,
        Auth $auth,
        Session $session,
        private readonly QuestionRepository $questions,
        private readonly ResponseRepository $responses,
        private readonly SurveyPolicy $policy,
    ) {
        parent::__construct($view, $auth, $session);
    }

    public function show(Request $request, int $surveyId): Response
    {
        [$user, $survey] = $this->authorize($surveyId);

        return $this->render('results/show', [
            'survey' => $survey,
            'summary' => $this->summary($survey),
            'responses' => $this->responses->forSurvey($survey->id),
            'prefix' => $this->prefix($user, $survey),
        ]);
    }

    public function response(Request $request, int $surveyId, int $responseId): Response
    {
        [$user, $survey] = $this->authorize($surveyId);
        $response = $this->responses->find($survey->id, $responseId) ?? throw HttpException::notFound();

        return $this->render('results/response', [
            'survey' => $survey,
            'response' => $response,
            'questions' => $this->questions->forSurvey($survey->id),
            'choices' => $this->responses->choices($responseId),
            'prefix' => $this->prefix($user, $survey),
        ]);
    }

    public function report(Request $request, int $surveyId): Response
    {
        [, $survey] = $this->authorize($surveyId);

        return Response::html($this->view->render('results/report', [
            'survey' => $survey,
            'summary' => $this->summary($survey),
            'generatedAt' => date('Y-m-d H:i:s'),
        ], 'report_layout'));
    }

    /**
     * @return array{0: User, 1: Survey}
     */
    private function authorize(int $surveyId): array
    {
        $user = $this->requireUser();

        return [$user, $this->policy->results($user, $surveyId)];
    }

    /**
     * @return list<array{question: Question, counts: array<string, int>, total: int}>
     */
    private function summary(Survey $survey): array
    {
        $tally = $this->responses->tally($survey->id);

        return array_map(static function (Question $question) use ($tally): array {
            $counts = [];
            foreach (array_keys($question->options()) as $letter) {
                $counts[$letter] = $tally[$question->id][$letter] ?? 0;
            }

            return ['question' => $question, 'counts' => $counts, 'total' => array_sum($counts)];
        }, $this->questions->forSurvey($survey->id));
    }

    private function prefix(User $user, Survey $survey): string
    {
        return $survey->isOwnedBy($user) ? "/my-surveys/{$survey->id}" : "/admin/surveys/{$survey->id}";
    }
}
