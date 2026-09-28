<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain\User;
use App\Http\AuthenticationRequired;
use App\Http\HttpException;
use App\Http\Response;
use App\Http\Session;
use App\Security\Auth;
use App\View\View;

abstract class Controller
{
    public function __construct(
        protected readonly View $view,
        protected readonly Auth $auth,
        protected readonly Session $session,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function render(string $template, array $data = [], int $status = 200): Response
    {
        return Response::html($this->view->render($template, $data), $status);
    }

    protected function redirect(string $path): Response
    {
        return Response::redirect(url($path));
    }

    protected function redirectWith(string $path, string $type, string $message): Response
    {
        $this->session->flash($type, $message);

        return $this->redirect($path);
    }

    protected function requireUser(): User
    {
        return $this->auth->user() ?? throw new AuthenticationRequired();
    }

    protected function requireAdmin(): User
    {
        $user = $this->requireUser();

        if (!$user->isAdmin()) {
            throw HttpException::forbidden();
        }

        return $user;
    }
}
