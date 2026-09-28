<?php

declare(strict_types=1);

use App\Controller\Admin\SurveyController as AdminSurveyController;
use App\Controller\AuthController;
use App\Controller\HomeController;
use App\Controller\ParticipationController;
use App\Controller\PaymentController;
use App\Controller\ProfileController;
use App\Controller\QuestionController;
use App\Controller\ResultsController;
use App\Controller\RewardController;
use App\Controller\SurveyController;
use App\Http\Router;

return static function (Router $router): void {
    $router->get('/', [HomeController::class, 'index']);

    $router->get('/login', [AuthController::class, 'showLogin']);
    $router->post('/login', [AuthController::class, 'login']);
    $router->get('/register', [AuthController::class, 'showRegister']);
    $router->post('/register', [AuthController::class, 'register']);
    $router->post('/logout', [AuthController::class, 'logout']);

    $router->get('/profile', [ProfileController::class, 'show']);
    $router->get('/profile/edit', [ProfileController::class, 'edit']);
    $router->post('/profile/edit', [ProfileController::class, 'update']);
    $router->get('/profile/password', [ProfileController::class, 'editPassword']);
    $router->post('/profile/password', [ProfileController::class, 'updatePassword']);

    $router->get('/rewards', [RewardController::class, 'index']);

    // Respondents
    $router->get('/surveys', [ParticipationController::class, 'index']);
    $router->get('/surveys/{survey}', [ParticipationController::class, 'show']);
    $router->post('/surveys/{survey}', [ParticipationController::class, 'submit']);

    // Researchers
    $router->get('/my-surveys', [SurveyController::class, 'index']);
    $router->get('/my-surveys/new', [SurveyController::class, 'create']);
    $router->post('/my-surveys/new', [SurveyController::class, 'store']);
    $router->get('/my-surveys/{survey}/edit', [SurveyController::class, 'edit']);
    $router->post('/my-surveys/{survey}/edit', [SurveyController::class, 'update']);
    $router->get('/my-surveys/{survey}/questions/new', [QuestionController::class, 'create']);
    $router->post('/my-surveys/{survey}/questions/new', [QuestionController::class, 'store']);
    $router->get('/my-surveys/{survey}/questions/{question}/edit', [QuestionController::class, 'edit']);
    $router->post('/my-surveys/{survey}/questions/{question}/edit', [QuestionController::class, 'update']);
    $router->post('/my-surveys/{survey}/questions/{question}/delete', [QuestionController::class, 'destroy']);
    $router->post('/my-surveys/{survey}/checkout', [PaymentController::class, 'checkout']);
    $router->get('/my-surveys/{survey}/payment/finish', [PaymentController::class, 'finish']);
    $router->get('/my-surveys/{survey}/results', [ResultsController::class, 'show']);
    $router->get('/my-surveys/{survey}/responses/{response}', [ResultsController::class, 'response']);
    $router->get('/my-surveys/{survey}/report', [ResultsController::class, 'report']);

    // Midtrans HTTP notification (configured in the Midtrans dashboard)
    $router->post('/payments/midtrans/notification', [PaymentController::class, 'notification']);

    // Administrators (the kernel enforces the role for every /admin path)
    $router->get('/admin', [AdminSurveyController::class, 'index']);
    $router->post('/admin/surveys/{survey}/close', [AdminSurveyController::class, 'close']);
    $router->post('/admin/surveys/{survey}/delete', [AdminSurveyController::class, 'destroy']);
    $router->get('/admin/surveys/{survey}/results', [ResultsController::class, 'show']);
    $router->get('/admin/surveys/{survey}/responses/{response}', [ResultsController::class, 'response']);
    $router->get('/admin/surveys/{survey}/report', [ResultsController::class, 'report']);
};
