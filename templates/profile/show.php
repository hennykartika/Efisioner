<?php
/**
 * @var App\View\View $view
 * @var App\Domain\User $user
 */
$view->set('title', 'Profile');
$rows = [
    'Username' => $user->username,
    'Email' => $user->email,
    'Phone number' => $user->phone ?? '-',
    'Date of birth' => $user->birthDate->format('j M Y'),
    'Gender' => $user->gender->label(),
    'Education' => $user->education->label(),
    'Occupation' => $user->occupationLabel(),
    'Institution' => $user->institution ?? '-',
    'Address' => $user->address ?? '-',
];
?>
<div class="row g-4">
    <div class="col-md-4">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <div class="avatar mx-auto mb-3" aria-hidden="true"><?= e($user->initials()) ?></div>
                <h1 class="h5 mb-1"><?= e($user->fullName) ?></h1>
                <p class="text-body-secondary small mb-3">@<?= e($user->username) ?></p>
                <a class="btn btn-brand btn-sm" href="<?= e(url('/profile/edit')) ?>">Edit profile</a>
                <a class="btn btn-outline-secondary btn-sm" href="<?= e(url('/profile/password')) ?>">Change password</a>
            </div>
        </div>
        <div class="card mt-4 shadow-sm">
            <div class="card-body">
                <p class="text-body-secondary small mb-1">Reward balance</p>
                <p class="h4 text-success mb-0"><?= e(rupiah($user->rewardBalance)) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <dl class="row mb-0">
                    <?php foreach ($rows as $label => $value): ?>
                        <dt class="col-sm-4 text-body-secondary fw-normal"><?= e($label) ?></dt>
                        <dd class="col-sm-8 text-pre-line"><?= e($value) ?></dd>
                    <?php endforeach; ?>
                </dl>
            </div>
        </div>
    </div>
</div>
