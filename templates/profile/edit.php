<?php
/**
 * @var App\View\View $view
 * @var App\Security\Csrf $csrf
 * @var array<string, string> $old
 * @var array<string, string> $errors
 */
$view->set('title', 'Edit profile');
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h4 mb-4">Edit profile</h1>
                <form method="post" action="<?= e(url('/profile/edit')) ?>">
                    <input type="hidden" name="_token" value="<?= e($csrf->token()) ?>">
                    <?= $view->renderFile('partials/profile_fields', ['old' => $old, 'errors' => $errors]) ?>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-brand">Save changes</button>
                        <a class="btn btn-outline-secondary" href="<?= e(url('/profile')) ?>">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
