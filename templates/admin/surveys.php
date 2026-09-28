<?php
/**
 * @var App\View\View $view
 * @var App\Security\Csrf $csrf
 * @var list<array{survey: App\Domain\Survey, owner_username: string}> $items
 * @var int $page
 * @var int $pages
 * @var int $total
 */
$view->set('title', 'Admin');
?>
<div class="d-flex justify-content-between align-items-baseline mb-4">
    <h1 class="h3 mb-0">All surveys</h1>
    <span class="text-body-secondary"><?= e($total) ?> in total</span>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Survey</th>
                <th scope="col">Owner</th>
                <th scope="col">Status</th>
                <th scope="col" class="text-end">Responses</th>
                <th scope="col"><span class="visually-hidden">Actions</span></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as ['survey' => $survey, 'owner_username' => $owner]): ?>
                <?php $status = $survey->status(); ?>
                <tr>
                    <td><?= e($survey->id) ?></td>
                    <td><?= e($survey->title) ?></td>
                    <td>@<?= e($owner) ?></td>
                    <td><span class="badge <?= e($status->badgeClass()) ?>"><?= e($status->label()) ?></span></td>
                    <td class="text-end"><?= e($survey->collectedResponses()) ?> / <?= e($survey->targetRespondents) ?></td>
                    <td class="text-end text-nowrap">
                        <?php if ($survey->isPaid()): ?>
                            <a class="btn btn-sm btn-outline-primary" href="<?= e(url("/admin/surveys/{$survey->id}/results")) ?>">Results</a>
                            <?php if (!$survey->isClosed()): ?>
                                <form method="post" action="<?= e(url("/admin/surveys/{$survey->id}/close")) ?>" class="d-inline" data-confirm="Close this survey? Respondents will no longer see it.">
                                    <input type="hidden" name="_token" value="<?= e($csrf->token()) ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-warning">Close</button>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <form method="post" action="<?= e(url("/admin/surveys/{$survey->id}/delete")) ?>" class="d-inline" data-confirm="Delete this draft permanently?">
                                <input type="hidden" name="_token" value="<?= e($csrf->token()) ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete draft</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if ($items === []): ?>
                <tr><td colspan="6" class="text-center text-body-secondary py-4">No surveys yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($pages > 1): ?>
    <nav class="mt-3" aria-label="Survey pages">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <li class="page-item<?= $i === $page ? ' active' : '' ?>">
                    <a class="page-link" href="<?= e(url('/admin') . '?page=' . $i) ?>"<?= $i === $page ? ' aria-current="page"' : '' ?>><?= e($i) ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>
