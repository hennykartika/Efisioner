<?php
/**
 * @var App\View\View $view
 * @var list<array{survey: App\Domain\Survey, question_count: int}> $items
 */
$view->set('title', 'My surveys');
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="h3 mb-1">My surveys</h1>
        <p class="text-body-secondary mb-0">Questionnaires you publish for other people to answer.</p>
    </div>
    <a class="btn btn-brand" href="<?= e(url('/my-surveys/new')) ?>">New survey</a>
</div>

<?php if ($items === []): ?>
    <div class="card card-body text-center">
        <p class="mb-3">You have not created a survey yet.</p>
        <p class="mb-0"><a class="btn btn-brand" href="<?= e(url('/my-surveys/new')) ?>">Create your first survey</a></p>
    </div>
<?php else: ?>
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                <tr>
                    <th scope="col">Survey</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-end">Questions</th>
                    <th scope="col" class="text-end">Responses</th>
                    <th scope="col" class="text-end">Reward</th>
                    <th scope="col"><span class="visually-hidden">Actions</span></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($items as ['survey' => $survey, 'question_count' => $questionCount]): ?>
                    <?php $status = $survey->status(); ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= e($survey->title) ?></div>
                            <div class="small text-body-secondary">Created <?= e(format_date($survey->createdAt)) ?></div>
                        </td>
                        <td><span class="badge <?= e($status->badgeClass()) ?>"><?= e($status->label()) ?></span></td>
                        <td class="text-end"><?= e($questionCount) ?></td>
                        <td class="text-end"><?= e($survey->collectedResponses()) ?> / <?= e($survey->targetRespondents) ?></td>
                        <td class="text-end text-nowrap"><?= e(rupiah($survey->rewardPerRespondent)) ?></td>
                        <td class="text-end text-nowrap">
                            <?php if ($survey->isPaid()): ?>
                                <a class="btn btn-sm btn-outline-primary" href="<?= e(url("/my-surveys/{$survey->id}/results")) ?>">Results</a>
                            <?php endif; ?>
                            <a class="btn btn-sm btn-outline-secondary" href="<?= e(url("/my-surveys/{$survey->id}/edit")) ?>">
                                <?= $survey->isPaid() ? 'Details' : 'Edit and pay' ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
