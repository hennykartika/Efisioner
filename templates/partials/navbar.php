<?php
/**
 * @var App\Domain\User|null $user
 * @var App\Security\Csrf $csrf
 * @var string $currentPath
 */
$currentPath ??= '/';
$isActive = static fn (string $prefix): bool => $prefix === '/'
    ? $currentPath === '/'
    : $currentPath === $prefix || str_starts_with($currentPath, $prefix . '/');
$link = static fn (string $path, string $label): string => sprintf(
    '<li class="nav-item"><a class="nav-link%s" href="%s"%s>%s</a></li>',
    $isActive($path) ? ' active' : '',
    e(url($path)),
    $isActive($path) ? ' aria-current="page"' : '',
    e($label),
);
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-brand">
    <div class="container">
        <a class="navbar-brand" href="<?= e(url('/')) ?>">
            <img src="<?= e(asset('img/icon.png')) ?>" alt="" width="32" height="32" class="rounded me-2">Efisioner
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-nav" aria-controls="main-nav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="main-nav">
            <ul class="navbar-nav me-auto">
                <?= $link('/', 'Home') ?>
                <?php if ($user !== null): ?>
                    <?= $link('/surveys', 'Answer surveys') ?>
                    <?= $link('/my-surveys', 'My surveys') ?>
                    <?= $link('/rewards', 'Rewards') ?>
                    <?php if ($user->isAdmin()): ?>
                        <?= $link('/admin', 'Admin') ?>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <?php if ($user === null): ?>
                    <?= $link('/login', 'Sign in') ?>
                    <li class="nav-item"><a class="btn btn-light btn-sm ms-lg-2 mt-2 mt-lg-0" href="<?= e(url('/register')) ?>">Create account</a></li>
                <?php else: ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><?= e($user->fullName) ?></a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= e(url('/profile')) ?>">Profile</a></li>
                            <li><a class="dropdown-item" href="<?= e(url('/profile/password')) ?>">Change password</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="post" action="<?= e(url('/logout')) ?>">
                                    <input type="hidden" name="_token" value="<?= e($csrf->token()) ?>">
                                    <button type="submit" class="dropdown-item">Sign out</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
