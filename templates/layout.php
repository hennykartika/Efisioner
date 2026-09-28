<?php
/**
 * @var App\View\View $view
 * @var App\Security\Auth $auth
 * @var App\Http\Session $session
 * @var string $content
 */
$title = $view->get('title');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title === '' ? 'Efisioner' : $title . ' · Efisioner') ?></title>
    <link rel="icon" type="image/png" href="<?= e(asset('img/icon.png')) ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body class="d-flex flex-column min-vh-100">
<?= $view->renderFile('partials/navbar', ['user' => $auth->user()]) ?>

<main class="flex-grow-1 py-4">
    <div class="container">
        <?= $view->renderFile('partials/flash', ['flashes' => $session->takeFlashes()]) ?>
        <?= $content ?>
    </div>
</main>

<?= $view->renderFile('partials/footer', []) ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<?php if ($view->get('charts') === 'yes'): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js" integrity="sha384-jb8JQMbMoBUzgWatfe6COACi2ljcDdZQ2OxczGA3bGNeWe+6DChMTBJemed7ZnvJ" crossorigin="anonymous"></script>
<?php endif; ?>
<script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
