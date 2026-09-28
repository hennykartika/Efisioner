<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain\Survey;
use App\Http\HttpException;
use App\Http\Request;
use App\Http\Response;
use App\Http\Session;
use App\Repository\QuestionRepository;
use App\Security\Auth;
use App\Security\SurveyPolicy;
use App\Validation\Validator;
use App\View\View;

final class QuestionController extends Controller
{
    private const FIELDS = ['body', 'option_a', 'option_b', 'option_c', 'option_d'];

    public function __construct(
        View $view,
        Auth $auth,
        Session $session,
        private readonly QuestionRepository $questions,
        private readonly SurveyPolicy $policy,
    ) {
        parent::__construct($view, $auth, $session);
    }

    public function create(Request $request, int $surveyId): Response
    {
        $survey = $this->editableSurvey($surveyId);

        return $this->render('surveys/question', [
            'survey' => $survey,
            'question' => null,
            'old' => array_fill_keys(self::FIELDS, ''),
            'errors' => [],
        ]);
    }

    public function store(Request $request, int $surveyId): Response
    {
        $survey = $this->editableSurvey($surveyId);
        $validator = $this->validate($request);

        if (!$validator->passes()) {
            return $this->render('surveys/question', [
                'survey' => $survey,
                'question' => null,
                'old' => $validator->data(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $this->questions->create($survey->id, $this->values($validator));

        return $this->redirectWith("/my-surveys/{$survey->id}/edit", 'success', 'Question added.');
    }

    public function edit(Request $request, int $surveyId, int $questionId): Response
    {
        $survey = $this->editableSurvey($surveyId);
        $question = $this->questions->find($survey->id, $questionId) ?? throw HttpException::notFound();

        return $this->render('surveys/question', [
            'survey' => $survey,
            'question' => $question,
            'old' => [
                'body' => $question->body,
                'option_a' => $question->optionA,
                'option_b' => $question->optionB,
                'option_c' => $question->optionC ?? '',
                'option_d' => $question->optionD ?? '',
            ],
            'errors' => [],
        ]);
    }

    public function update(Request $request, int $surveyId, int $questionId): Response
    {
        $survey = $this->editableSurvey($surveyId);
        $question = $this->questions->find($survey->id, $questionId) ?? throw HttpException::notFound();
        $validator = $this->validate($request);

        if (!$validator->passes()) {
            return $this->render('surveys/question', [
                'survey' => $survey,
                'question' => $question,
                'old' => $validator->data(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $this->questions->update($survey->id, $question->id, $this->values($validator));

        return $this->redirectWith("/my-surveys/{$survey->id}/edit", 'success', 'Question updated.');
    }

    public function destroy(Request $request, int $surveyId, int $questionId): Response
    {
        $survey = $this->editableSurvey($surveyId);
        $this->questions->delete($survey->id, $questionId);

        return $this->redirectWith("/my-surveys/{$survey->id}/edit", 'success', 'Question deleted.');
    }

    private function editableSurvey(int $surveyId): Survey
    {
        $survey = $this->policy->owned($this->requireUser(), $surveyId);

        if (!$survey->canChangeContent()) {
            throw new HttpException(409, 'Questions cannot be changed after respondents have started answering.', 'Survey locked');
        }

        return $survey;
    }

    private function validate(Request $request): Validator
    {
        $validator = new Validator($request->only(self::FIELDS));
        $validator
            ->required('body', 'Question')
            ->length('body', 'Question', 3, 500)
            ->required('option_a', 'Option A')
            ->length('option_a', 'Option A', 1, 200)
            ->required('option_b', 'Option B')
            ->length('option_b', 'Option B', 1, 200)
            ->length('option_c', 'Option C', 1, 200)
            ->length('option_d', 'Option D', 1, 200);

        if ($validator->value('option_d') !== '' && $validator->value('option_c') === '') {
            $validator->addError('option_c', 'Fill in option C before option D.');
        }

        return $validator;
    }

    /**
     * @return array{body: string, option_a: string, option_b: string, option_c: ?string, option_d: ?string}
     */
    private function values(Validator $validator): array
    {
        return [
            'body' => $validator->value('body'),
            'option_a' => $validator->value('option_a'),
            'option_b' => $validator->value('option_b'),
            'option_c' => $validator->nullable('option_c'),
            'option_d' => $validator->nullable('option_d'),
        ];
    }
}
