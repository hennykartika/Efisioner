<?php
/**
 * @var App\View\View $view
 * @var App\Security\Csrf $csrf
 * @var App\Domain\Survey $survey
 * @var App\Domain\Question|null $question
 * @var array<string, string> $old
 * @var array<string, string> $errors
 */
$isNew = $question === null;
$heading = $isNew ? 'Add question' : 'Edit question';
$view->set('title', $heading);
$base = "/my-surveys/{$survey->id}";
$action = $isNew ? "{$base}/questions/new" : "{$base}/questions/{$question->id}/edit";
$field = static fn (string $name, string $label, bool $required): string => $view->renderFile('partials/field', [
    'name' => $name,
    'label' => $label,
    'value' => $old[$name] ?? '',
    'error' => $errors[$name] ?? null,
    'attributes' => ['required' => $required, 'maxlength' => 200],
]);
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= e(url('/my-surveys')) ?>">My surveys</a></li>
                <li class="breadcrumb-item"><a href="<?= e(url($base . '/edit')) ?>"><?= e($survey->title) ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= e($heading) ?></li>
            </ol>
        </nav>
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h4 mb-4"><?= e($heading) ?></h1>
                <form method="post" action="<?= e(url($action)) ?>">
                    <input type="hidden" name="_token" value="<?= e($csrf->token()) ?>">
                    <?= $view->renderFile('partials/field', [
                        'name' => 'body',
                        'label' => 'Question',
                        'type' => 'textarea',
                        'value' => $old['body'] ?? '',
                        'error' => $errors['body'] ?? null,
                        'attributes' => ['required' => true, 'rows' => 3, 'maxlength' => 500],
                    ]) ?>
                    <div class="row">
                        <div class="col-md-6"><?= $field('option_a', 'Option A', true) ?></div>
                        <div class="col-md-6"><?= $field('option_b', 'Option B', true) ?></div>
                        <div class="col-md-6"><?= $field('option_c', 'Option C (optional)', false) ?></div>
                        <div class="col-md-6"><?= $field('option_d', 'Option D (optional)', false) ?></div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-brand"><?= $isNew ? 'Add question' : 'Save question' ?></button>
                        <a class="btn btn-outline-secondary" href="<?= e(url($base . '/edit')) ?>">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
