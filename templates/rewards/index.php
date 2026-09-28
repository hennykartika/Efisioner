<?php
/**
 * @var App\View\View $view
 * @var int $balance
 * @var list<array<string, mixed>> $history
 */
$view->set('title', 'Rewards');
?>
<div class="balance-card rounded-4 text-white text-center p-4 mb-4">
    <p class="mb-1">Reward balance</p>
    <p class="display-5 fw-semibold mb-0"><?= e(rupiah($balance)) ?></p>
</div>

<div class="card shadow-sm">
    <div class="card-header">History</div>
    <?php if ($history === []): ?>
        <div class="card-body text-body-secondary">
            No rewards yet. <a href="<?= e(url('/surveys')) ?>">Answer a survey</a> to earn your first one.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th scope="col">Description</th>
                    <th scope="col">Date</th>
                    <th scope="col" class="text-end">Amount</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($history as $entry): ?>
                    <?php $amount = (int) $entry['amount']; ?>
                    <tr>
                        <td><?= e($entry['description']) ?></td>
                        <td class="text-nowrap"><?= e(format_datetime($entry['created_at'])) ?></td>
                        <td class="text-end fw-semibold <?= $amount >= 0 ? 'text-success' : 'text-danger' ?>">
                            <?= e(($amount >= 0 ? '+' : '-') . rupiah(abs($amount))) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
