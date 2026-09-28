<?php
/**
 * @var App\View\View $view
 * @var App\Security\Csrf $csrf
 * @var array<string, string> $old
 * @var array<string, string> $errors
 * @var int $platformFee
 */
$view->set('title', 'New survey');
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= e(url('/my-surveys')) ?>">My surveys</a></li>
                <li class="breadcrumb-item active" aria-current="page">New survey</li>
            </ol>
        </nav>
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h4 mb-1">New survey</h1>
                <p class="text-body-secondary mb-4">Start with the basics. You will add questions on the next page and pay once the survey is ready.</p>
                <form method="post" action="<?= e(url('/my-surveys/new')) ?>">
                    <input type="hidden" name="_token" value="<?= e($csrf->token()) ?>">
                    <?= $view->renderFile('partials/survey_fields', ['old' => $old, 'errors' => $errors, 'platformFee' => $platformFee]) ?>
                    <button type="submit" class="btn btn-brand">Create survey</button>
                </form>
            </div>
        </div>
    </div>
</div>
