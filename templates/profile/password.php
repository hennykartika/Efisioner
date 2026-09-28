<?php
/**
 * @var App\View\View $view
 * @var App\Security\Csrf $csrf
 * @var array<string, string> $errors
 */
use App\Security\PasswordHasher;

$view->set('title', 'Change password');
$field = static fn (string $name, string $label, string $autocomplete, ?string $help = null): string => $view->renderFile('partials/field', [
    'name' => $name,
    'label' => $label,
    'type' => 'password',
    'error' => $errors[$name] ?? null,
    'help' => $help,
    'attributes' => ['required' => true, 'autocomplete' => $autocomplete],
]);
?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h4 mb-4">Change password</h1>
                <form method="post" action="<?= e(url('/profile/password')) ?>">
                    <input type="hidden" name="_token" value="<?= e($csrf->token()) ?>">
                    <?= $field('current_password', 'Current password', 'current-password') ?>
                    <?= $field('password', 'New password', 'new-password', sprintf('At least %d characters.', PasswordHasher::MIN_LENGTH)) ?>
                    <?= $field('password_confirmation', 'Confirm new password', 'new-password') ?>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-brand">Update password</button>
                        <a class="btn btn-outline-secondary" href="<?= e(url('/profile')) ?>">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
