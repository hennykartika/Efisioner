<?php
/**
 * @var App\View\View $view
 * @var list<array<string, mixed>> $surveys
 */
$view->set('title', 'Answer surveys');
?>
<h1 class="h3 mb-1">Answer surveys</h1>
<p class="text-body-secondary mb-4">Questionnaires you have not answered yet. Each completed response adds the reward to your balance.</p>

<?php if ($surveys === []): ?>
    <div class="card card-body text-center text-body-secondary">
        Nothing new for you right now. Come back later or <a href="<?= e(url('/my-surveys/new')) ?>">publish your own survey</a>.
    </div>
<?php else: ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($surveys as $survey): ?>
            <?= $view->renderFile('partials/survey_card', ['survey' => $survey]) ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
