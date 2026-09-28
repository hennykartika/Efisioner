<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Controller;
use App\Http\HttpException;
use App\Http\Request;
use App\Http\Response;
use App\Http\Session;
use App\Repository\SurveyRepository;
use App\Security\Auth;
use App\View\View;

final class SurveyController extends Controller
{
    private const PER_PAGE = 25;

    public function __construct(
        View $view,
        Auth $auth,
        Session $session,
        private readonly SurveyRepository $surveys,
    ) {
        parent::__construct($view, $auth, $session);
    }

    public function index(Request $request): Response
    {
        $this->requireAdmin();

        $total = $this->surveys->count();
        $pages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = min($pages, max(1, (int) ($request->query('page') ?? 1)));

        return $this->render('admin/surveys', [
            'items' => $this->surveys->paginate($page, self::PER_PAGE),
            'page' => $page,
            'pages' => $pages,
            'total' => $total,
        ]);
    }

    public function close(Request $request, int $surveyId): Response
    {
        $this->requireAdmin();
        $survey = $this->surveys->find($surveyId) ?? throw HttpException::notFound();

        if (!$survey->isPaid()) {
            return $this->redirectWith('/admin', 'danger', 'Only published surveys can be closed. Delete the draft instead.');
        }

        $this->surveys->close($survey->id);

        return $this->redirectWith('/admin', 'success', sprintf('"%s" is closed and hidden from respondents.', $survey->title));
    }

    public function destroy(Request $request, int $surveyId): Response
    {
        $this->requireAdmin();
        $survey = $this->surveys->find($surveyId) ?? throw HttpException::notFound();

        if (!$this->surveys->deleteDraft($survey->id)) {
            return $this->redirectWith('/admin', 'danger', 'Only unpaid drafts without a checkout attempt can be deleted. Close the survey instead.');
        }

        return $this->redirectWith('/admin', 'success', sprintf('Draft "%s" was deleted.', $survey->title));
    }
}
