<?php
/**
 * @var App\View\View $view
 * @var App\Domain\Survey $survey
 * @var array<string, mixed> $response
 * @var list<App\Domain\Question> $questions
 * @var array<int, string> $choices
 * @var string $prefix
 */
use App\Domain\Education;
use App\Domain\Gender;
use App\Domain\Occupation;

$view->set('title', 'Response from ' . $response['respondent_name']);
$details = [
    'Name' => $response['respondent_name'],
    'Gender' => Gender::from($response['gender'])->label(),
    'Age' => (string) $response['age'],
    'Education' => Education::from($response['education'])->label(),
    'Occupation' => Occupation::describe($response['occupation'], $response['occupation_other']),
    'Institution' => $response['institution'] ?? '-',
    'Submitted' => format_datetime($response['submitted_at']),
];
?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= e(url($prefix . '/results')) ?>"><?= e($survey->title) ?></a></li>
        <li class="breadcrumb-item active" aria-current="page">Response</li>
    </ol>
</nav>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header">Respondent</div>
            <div class="card-body">
                <dl class="mb-0 small">
                    <?php foreach ($details as $label => $value): ?>
                        <dt class="text-body-secondary fw-normal"><?= e($label) ?></dt>
                        <dd><?= e($value) ?></dd>
                    <?php endforeach; ?>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <?php foreach ($questions as $number => $question): ?>
            <?php $chosen = $choices[$question->id] ?? null; ?>
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h2 class="h6"><?= e($number + 1) ?>. <?= e($question->body) ?></h2>
                    <ul class="list-group">
                        <?php foreach ($question->options() as $letter => $text): ?>
                            <li class="list-group-item<?= $chosen === $letter ? ' list-group-item-success fw-semibold' : '' ?>">
                                <?= e($letter) ?>. <?= e($text) ?>
                                <?php if ($chosen === $letter): ?><span class="visually-hidden">(selected)</span><?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if ($chosen === null): ?>
                        <p class="small text-body-secondary mt-2 mb-0">No answer recorded for this question.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
