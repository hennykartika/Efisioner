<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;
use App\Http\Session;
use App\Repository\UserRepository;
use App\Security\Auth;
use App\Security\LoginThrottle;
use App\Security\PasswordHasher;
use App\Support\Database;
use App\Validation\ProfileForm;
use App\Validation\Validator;
use App\View\View;
use DateTimeImmutable;
use PDOException;

final class AuthController extends Controller
{
    public function __construct(
        View $view,
        Auth $auth,
        Session $session,
        private readonly UserRepository $users,
        private readonly PasswordHasher $hasher,
        private readonly LoginThrottle $throttle,
    ) {
        parent::__construct($view, $auth, $session);
    }

    public function showLogin(): Response
    {
        if ($this->auth->user() !== null) {
            return $this->redirect('/');
        }

        return $this->render('auth/login', ['username' => '', 'error' => null]);
    }

    public function login(Request $request): Response
    {
        $username = mb_strtolower(trim($request->input('username') ?? ''));
        $password = $request->input('password') ?? '';
        $ip = $request->ip();

        if ($this->throttle->tooManyAttempts($username, $ip)) {
            return $this->render('auth/login', [
                'username' => $username,
                'error' => sprintf('Too many failed attempts. Please wait %d minutes and try again.', LoginThrottle::windowMinutes()),
            ], 429);
        }

        $user = $username !== '' && $password !== '' ? $this->auth->attempt($username, $password) : null;

        if ($user === null) {
            $this->throttle->recordFailure($username, $ip);

            return $this->render('auth/login', ['username' => $username, 'error' => 'Incorrect username or password.'], 422);
        }

        $this->throttle->clear($username, $ip);
        $this->auth->login($user);

        $intended = $this->session->pull('intended_url');

        return Response::redirect(is_string($intended) && self::isLocalPath($intended) ? $intended : url('/'));
    }

    public function showRegister(): Response
    {
        if ($this->auth->user() !== null) {
            return $this->redirect('/');
        }

        return $this->render('auth/register', ['old' => [], 'errors' => []]);
    }

    public function register(Request $request): Response
    {
        $input = ProfileForm::normalize($request->only([...ProfileForm::FIELDS, 'username']));
        $input['username'] = mb_strtolower(trim($input['username']));
        $password = $request->input('password') ?? '';

        $validator = new Validator($input);
        $validator
            ->required('username', 'Username')
            ->length('username', 'Username', 3, 32)
            ->pattern('username', '/^[a-z0-9_.]+$/', 'Username may only contain letters, numbers, dots and underscores.');
        ProfileForm::validate($validator, new DateTimeImmutable('today'));

        if (($error = PasswordHasher::policyError($password)) !== null) {
            $validator->addError('password', $error);
        } elseif ($password !== ($request->input('password_confirmation') ?? '')) {
            $validator->addError('password_confirmation', 'Password confirmation does not match.');
        }

        if (!$validator->hasError('username') && $this->users->usernameExists($validator->value('username'))) {
            $validator->addError('username', 'This username is already taken.');
        }

        if (!$validator->hasError('email') && $this->users->emailExists($validator->value('email'))) {
            $validator->addError('email', 'An account with this email already exists.');
        }

        if (!$validator->passes()) {
            return $this->render('auth/register', ['old' => $validator->data(), 'errors' => $validator->errors()], 422);
        }

        try {
            $userId = $this->users->create(
                $validator->value('username'),
                $this->hasher->hash($password),
                ProfileForm::values($validator),
            );
        } catch (PDOException $e) {
            if (!Database::isDuplicateKey($e)) {
                throw $e;
            }

            return $this->render('auth/register', [
                'old' => $validator->data(),
                'errors' => ['username' => 'This username or email is already registered.'],
            ], 422);
        }

        $user = $this->users->find($userId);
        if ($user !== null) {
            $this->auth->login($user);
        }

        return $this->redirectWith('/', 'success', 'Welcome to Efisioner! Your account is ready.');
    }

    public function logout(): Response
    {
        $this->auth->logout();

        return $this->redirectWith('/', 'success', 'You have been signed out.');
    }

    private static function isLocalPath(string $path): bool
    {
        return str_starts_with($path, '/') && !str_starts_with($path, '//') && !str_starts_with($path, '/\\');
    }
}
