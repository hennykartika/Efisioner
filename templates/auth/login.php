<?php
/**
 * @var App\View\View $view
 * @var App\Security\Csrf $csrf
 * @var string $username
 * @var string|null $error
 */
$view->set('title', 'Sign in');
?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h4 mb-4 text-center">Sign in to Efisioner</h1>

                <?php if ($error !== null): ?>
                    <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
                <?php endif; ?>

                <form method="post" action="<?= e(url('/login')) ?>">
                    <input type="hidden" name="_token" value="<?= e($csrf->token()) ?>">
                    <?= $view->renderFile('partials/field', [
                        'name' => 'username',
                        'label' => 'Username',
                        'value' => $username,
                        'attributes' => ['required' => true, 'autocomplete' => 'username', 'autofocus' => $username === ''],
                    ]) ?>
                    <?= $view->renderFile('partials/field', [
                        'name' => 'password',
                        'label' => 'Password',
                        'type' => 'password',
                        'attributes' => ['required' => true, 'autocomplete' => 'current-password', 'autofocus' => $username !== ''],
                    ]) ?>
                    <button type="submit" class="btn btn-brand w-100">Sign in</button>
                </form>

                <p class="text-center small mt-4 mb-0">
                    New here? <a href="<?= e(url('/register')) ?>">Create an account</a>
                </p>
            </div>
        </div>
    </div>
</div>
