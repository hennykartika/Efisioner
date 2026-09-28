<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Response;
use App\Http\Session;
use App\Repository\SurveyRepository;
use App\Security\Auth;
use App\View\View;

final class HomeController extends Controller
{
    public function __construct(
        View $view,
        Auth $auth,
        Session $session,
        private readonly SurveyRepository $surveys,
    ) {
        parent::__construct($view, $auth, $session);
    }

    public function index(): Response
    {
        return $this->render('home/index', [
            'surveys' => $this->surveys->listOpen($this->auth->user()?->id, 6),
        ]);
    }
}
