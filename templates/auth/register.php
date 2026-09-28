<?php
/**
 * @var App\View\View $view
 * @var App\Security\Csrf $csrf
 * @var array<string, string> $old
 * @var array<string, string> $errors
 */
use App\Security\PasswordHasher;

$view->set('title', 'Create account');
$old += ['username' => ''];
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h4 mb-1">Create your account</h1>
                <p class="text-body-secondary mb-4">Your profile is shared with researchers as part of each response, so please keep it accurate.</p>

                <form method="post" action="<?= e(url('/register')) ?>">
                    <input type="hidden" name="_token" value="<?= e($csrf->token()) ?>">

                    <h2 class="h6 text-uppercase text-body-secondary mb-3">Sign-in details</h2>
                    <?= $view->renderFile('partials/field', [
                        'name' => 'username',
                        'label' => 'Username',
                        'value' => $old['username'],
                        'error' => $errors['username'] ?? null,
                        'help' => 'Letters, numbers, dots and underscores. Cannot be changed later.',
                        'attributes' => ['required' => true, 'minlength' => 3, 'maxlength' => 32, 'autocomplete' => 'username'],
                    ]) ?>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $view->renderFile('partials/field', [
                                'name' => 'password',
                                'label' => 'Password',
                                'type' => 'password',
                                'error' => $errors['password'] ?? null,
                                'help' => sprintf('At least %d characters.', PasswordHasher::MIN_LENGTH),
                                'attributes' => ['required' => true, 'minlength' => PasswordHasher::MIN_LENGTH, 'autocomplete' => 'new-password'],
                            ]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $view->renderFile('partials/field', [
                                'name' => 'password_confirmation',
                                'label' => 'Confirm password',
                                'type' => 'password',
                                'error' => $errors['password_confirmation'] ?? null,
                                'attributes' => ['required' => true, 'autocomplete' => 'new-password'],
                            ]) ?>
                        </div>
                    </div>

                    <h2 class="h6 text-uppercase text-body-secondary mt-2 mb-3">About you</h2>
                    <?= $view->renderFile('partials/profile_fields', ['old' => $old, 'errors' => $errors]) ?>

                    <button type="submit" class="btn btn-brand w-100">Create account</button>
                </form>

                <p class="text-center small mt-4 mb-0">
                    Already registered? <a href="<?= e(url('/login')) ?>">Sign in</a>
                </p>
            </div>
        </div>
    </div>
</div>
