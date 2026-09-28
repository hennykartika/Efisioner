<?php
/**
 * @var App\View\View $view
 * @var App\Domain\Survey $survey
 * @var list<array{question: App\Domain\Question, counts: array<string, int>, total: int}> $summary
 * @var string $generatedAt
 */
$view->set('title', 'Report: ' . $survey->title);
?>
<div class="d-flex justify-content-end mb-3 d-print-none">
    <button type="button" class="btn btn-brand" data-print>Print or save as PDF</button>
</div>

<header class="text-center mb-4">
    <p class="text-uppercase text-body-secondary small mb-1">Efisioner survey report</p>
    <h1 class="h3"><?= e($survey->title) ?></h1>
    <p class="small text-body-secondary mb-0">
        <?= e($survey->collectedResponses()) ?> of <?= e($survey->targetRespondents) ?> responses
        &middot; Generated <?= e(format_datetime($generatedAt)) ?>
    </p>
</header>

<?php foreach ($summary as $index => $item): ?>
    <?= $view->renderFile('partials/question_summary', ['number' => $index + 1, 'item' => $item]) ?>
<?php endforeach; ?>
