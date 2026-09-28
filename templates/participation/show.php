<?php
/**
 * @var App\View\View $view
 * @var App\Security\Csrf $csrf
 * @var App\Domain\Survey $survey
 * @var list<App\Domain\Question> $questions
 * @var App\Domain\User $respondent
 * @var int $age
 * @var array<int|string, string> $choices
 * @var array<string, string> $errors
 */
$view->set('title', $survey->title);
?>
<div class="row justify-content-center">
    <div class="col-lg-9">
        <h1 class="h3 mb-1"><?= e($survey->title) ?></h1>
        <p class="text-success fw-semibold mb-3">Reward: <?= e(rupiah($survey->rewardPerRespondent)) ?></p>
        <div class="card card-body mb-4 text-pre-line"><?= e($survey->description) ?></div>

        <div class="card mb-4">
            <div class="card-header">Details shared with the researcher</div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-sm-4">Name</dt><dd class="col-sm-8"><?= e($respondent->fullName) ?></dd>
                    <dt class="col-sm-4">Gender</dt><dd class="col-sm-8"><?= e($respondent->gender->label()) ?></dd>
                    <dt class="col-sm-4">Age</dt><dd class="col-sm-8"><?= e($age) ?></dd>
                    <dt class="col-sm-4">Education</dt><dd class="col-sm-8"><?= e($respondent->education->label()) ?></dd>
                    <dt class="col-sm-4">Occupation</dt><dd class="col-sm-8"><?= e($respondent->occupationLabel()) ?></dd>
                    <dt class="col-sm-4">Institution</dt><dd class="col-sm-8 mb-0"><?= e($respondent->institution ?? '-') ?></dd>
                </dl>
                <p class="small text-body-secondary mt-3 mb-0">Something wrong? <a href="<?= e(url('/profile/edit')) ?>">Update your profile</a> before answering.</p>
            </div>
        </div>

        <?php if ($errors !== []): ?>
            <div class="alert alert-danger" role="alert">Please answer every question before submitting.</div>
        <?php endif; ?>

        <form method="post" action="<?= e(url('/surveys/' . $survey->id)) ?>" data-confirm="Submit your answers? They cannot be changed afterwards.">
            <input type="hidden" name="_token" value="<?= e($csrf->token()) ?>">

            <?php foreach ($questions as $number => $question): ?>
                <?php $error = $errors['q' . $question->id] ?? null; ?>
                <fieldset class="card mb-3<?= $error !== null ? ' border-danger' : '' ?>">
                    <div class="card-body">
                        <legend class="h6 mb-3"><?= e($number + 1) ?>. <?= e($question->body) ?></legend>
                        <?php foreach ($question->options() as $letter => $text): ?>
                            <?php $inputId = sprintf('q%d-%s', $question->id, $letter); ?>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" required
                                       name="answers[<?= e($question->id) ?>]"
                                       id="<?= e($inputId) ?>"
                                       value="<?= e($letter) ?>"<?= ($choices[$question->id] ?? null) === $letter ? ' checked' : '' ?>>
                                <label class="form-check-label" for="<?= e($inputId) ?>"><?= e($letter) ?>. <?= e($text) ?></label>
                            </div>
                        <?php endforeach; ?>
                        <?php if ($error !== null): ?>
                            <div class="text-danger small mt-2"><?= e($error) ?></div>
                        <?php endif; ?>
                    </div>
                </fieldset>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-brand btn-lg">Submit answers</button>
        </form>
    </div>
</div>
