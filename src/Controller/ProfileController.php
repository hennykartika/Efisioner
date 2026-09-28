<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;
use App\Http\Session;
use App\Repository\UserRepository;
use App\Security\Auth;
use App\Security\Csrf;
use App\Security\PasswordHasher;
use App\Validation\ProfileForm;
use App\Validation\Validator;
use App\View\View;
use DateTimeImmutable;

final class ProfileController extends Controller
{
    public function __construct(
        View $view,
        Auth $auth,
        Session $session,
        private readonly UserRepository $users,
        private readonly PasswordHasher $hasher,
        private readonly Csrf $csrf,
    ) {
        parent::__construct($view, $auth, $session);
    }

    public function show(): Response
    {
        return $this->render('profile/show', ['user' => $this->requireUser()]);
    }

    public function edit(): Response
    {
        $user = $this->requireUser();

        return $this->render('profile/edit', [
            'old' => [
                'full_name' => $user->fullName,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
                'address' => $user->address ?? '',
                'birth_date' => $user->birthDate->format('Y-m-d'),
                'gender' => $user->gender->value,
                'education' => $user->education->value,
                'occupation' => $user->occupation->value,
                'occupation_other' => $user->occupationOther ?? '',
                'institution' => $user->institution ?? '',
            ],
            'errors' => [],
        ]);
    }

    public function update(Request $request): Response
    {
        $user = $this->requireUser();

        $validator = new Validator(ProfileForm::normalize($request->only(ProfileForm::FIELDS)));
        ProfileForm::validate($validator, new DateTimeImmutable('today'));

        if (!$validator->hasError('email') && $this->users->emailExists($validator->value('email'), $user->id)) {
            $validator->addError('email', 'Another account already uses this email.');
        }

        if (!$validator->passes()) {
            return $this->render('profile/edit', ['old' => $validator->data(), 'errors' => $validator->errors()], 422);
        }

        $this->users->updateProfile($user->id, ProfileForm::values($validator));

        return $this->redirectWith('/profile', 'success', 'Your profile has been updated.');
    }

    public function editPassword(): Response
    {
        $this->requireUser();

        return $this->render('profile/password', ['errors' => []]);
    }

    public function updatePassword(Request $request): Response
    {
        $user = $this->requireUser();

        $current = $request->input('current_password') ?? '';
        $new = $request->input('password') ?? '';
        $errors = [];

        if (!$this->hasher->verify($current, $this->users->passwordHash($user->id))) {
            $errors['current_password'] = 'Your current password is incorrect.';
        } elseif (($policyError = PasswordHasher::policyError($new)) !== null) {
            $errors['password'] = $policyError;
        } elseif ($new !== ($request->input('password_confirmation') ?? '')) {
            $errors['password_confirmation'] = 'Password confirmation does not match.';
        } elseif ($new === $current) {
            $errors['password'] = 'Choose a password different from the current one.';
        }

        if ($errors !== []) {
            return $this->render('profile/password', ['errors' => $errors], 422);
        }

        $this->users->updatePasswordHash($user->id, $this->hasher->hash($new));

        // A password change is a privilege event: issue a fresh session.
        $this->session->regenerate();
        $this->csrf->rotate();

        return $this->redirectWith('/profile', 'success', 'Your password has been changed.');
    }
}
