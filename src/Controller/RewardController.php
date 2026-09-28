<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Response;
use App\Http\Session;
use App\Repository\RewardRepository;
use App\Security\Auth;
use App\View\View;

final class RewardController extends Controller
{
    public function __construct(
        View $view,
        Auth $auth,
        Session $session,
        private readonly RewardRepository $rewards,
    ) {
        parent::__construct($view, $auth, $session);
    }

    public function index(): Response
    {
        $user = $this->requireUser();

        return $this->render('rewards/index', [
            'balance' => $user->rewardBalance,
            'history' => $this->rewards->history($user->id),
        ]);
    }
}
