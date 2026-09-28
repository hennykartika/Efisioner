<?php
/**
 * @var App\View\View $view
 * @var App\Security\Auth $auth
 * @var list<array<string, mixed>> $surveys
 */
$user = $auth->user();
?>
<section class="hero rounded-4 p-4 p-md-5 mb-5 text-white">
    <div class="row align-items-center g-4">
        <div class="col-lg-8">
            <h1 class="display-6 fw-semibold">Answer research questionnaires. Get rewarded.</h1>
            <p class="lead mb-4">
                Researchers publish questionnaires with a reward for every respondent.
                You pick the ones you want to answer and collect the reward in your balance.
            </p>
            <?php if ($user === null): ?>
                <a class="btn btn-light btn-lg me-2" href="<?= e(url('/register')) ?>">Join as respondent</a>
                <a class="btn btn-outline-light btn-lg" href="<?= e(url('/login')) ?>">Sign in</a>
            <?php else: ?>
                <a class="btn btn-light btn-lg me-2" href="<?= e(url('/surveys')) ?>">Find a survey</a>
                <a class="btn btn-outline-light btn-lg" href="<?= e(url('/my-surveys/new')) ?>">Publish a survey</a>
            <?php endif; ?>
        </div>
        <div class="col-lg-4 d-none d-lg-block text-center">
            <img src="<?= e(asset('img/icon.png')) ?>" alt="" class="img-fluid rounded-4 hero-logo">
        </div>
    </div>
</section>

<section>
    <div class="d-flex justify-content-between align-items-baseline mb-3">
        <h2 class="h4 mb-0">Open questionnaires</h2>
        <?php if ($user !== null && $surveys !== []): ?>
            <a href="<?= e(url('/surveys')) ?>">See all</a>
        <?php endif; ?>
    </div>

    <?php if ($surveys === []): ?>
        <p class="text-body-secondary">There are no open questionnaires right now. Check back soon.</p>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($surveys as $survey): ?>
                <?= $view->renderFile('partials/survey_card', ['survey' => $survey]) ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
