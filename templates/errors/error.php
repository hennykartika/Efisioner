<?php
/**
 * @var App\View\View $view
 * @var int $status
 * @var string $title
 * @var string $message
 * @var Throwable|null $exception only set when APP_DEBUG is on
 */
$view->set('title', $title);
?>
<div class="text-center py-5">
    <p class="display-4 fw-semibold text-body-secondary"><?= e($status) ?></p>
    <h1 class="h3"><?= e($title) ?></h1>
    <p class="text-body-secondary"><?= e($message) ?></p>
    <a class="btn btn-brand mt-2" href="<?= e(url('/')) ?>">Back to home</a>
</div>

<?php if ($exception !== null): ?>
    <div class="card border-danger">
        <div class="card-header text-danger">Debug details (APP_DEBUG is on)</div>
        <div class="card-body">
            <p class="fw-semibold"><?= e($exception::class) ?>: <?= e($exception->getMessage()) ?></p>
            <p class="small"><?= e($exception->getFile()) ?>:<?= e($exception->getLine()) ?></p>
            <pre class="small mb-0"><?= e($exception->getTraceAsString()) ?></pre>
        </div>
    </div>
<?php endif; ?>
