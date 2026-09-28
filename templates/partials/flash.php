<?php
/** @var list<array{type: string, message: string}> $flashes */
$allowed = ['success', 'danger', 'info', 'warning'];
?>
<?php foreach ($flashes as $flash): ?>
    <div class="alert alert-<?= e(in_array($flash['type'], $allowed, true) ? $flash['type'] : 'info') ?> alert-dismissible fade show" role="alert">
        <?= e($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endforeach; ?>
