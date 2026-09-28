<?php
/**
 * @var App\View\View $view
 * @var App\Domain\Survey $survey
 * @var list<array{question: App\Domain\Question, counts: array<string, int>, total: int}> $summary
 * @var list<array<string, mixed>> $responses
 * @var string $prefix
 */
use App\Domain\Education;
use App\Domain\Gender;
use App\Domain\Occupation;

$view->set('title', 'Results: ' . $survey->title);
$view->set('charts', 'yes');
$status = $survey->status();
$isAdminView = str_starts_with($prefix, '/admin');
?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <?php if ($isAdminView): ?>
            <li class="breadcrumb-item"><a href="<?= e(url('/admin')) ?>">Admin</a></li>
        <?php else: ?>
            <li class="breadcrumb-item"><a href="<?= e(url('/my-surveys')) ?>">My surveys</a></li>
            <li class="breadcrumb-item"><a href="<?= e(url($prefix . '/edit')) ?>"><?= e($survey->title) ?></a></li>
        <?php endif; ?>
        <li class="breadcrumb-item active" aria-current="page">Results</li>
    </ol>
</nav>

<div class="d-flex flex-wrap align-items-center gap-2 mb-4">
    <h1 class="h3 mb-0 me-2"><?= e($survey->title) ?></h1>
    <span class="badge <?= e($status->badgeClass()) ?>"><?= e($status->label()) ?></span>
    <a class="btn btn-outline-secondary btn-sm ms-auto" href="<?= e(url($prefix . '/report')) ?>" target="_blank" rel="noopener">Printable report</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card card-body text-center">
            <div class="small text-body-secondary">Responses</div>
            <div class="h4 mb-0"><?= e($survey->collectedResponses()) ?> / <?= e($survey->targetRespondents) ?></div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card card-body text-center">
            <div class="small text-body-secondary">Reward per respondent</div>
            <div class="h4 mb-0"><?= e(rupiah($survey->rewardPerRespondent)) ?></div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card card-body text-center">
            <div class="small text-body-secondary">Published</div>
            <div class="h4 mb-0"><?= e(format_date($survey->paidAt)) ?></div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header">Answer summary</div>
    <div class="card-body">
        <?php if ($summary === []): ?>
            <p class="text-body-secondary mb-0">This survey has no questions.</p>
        <?php endif; ?>
        <?php foreach ($summary as $index => $item): ?>
            <?= $view->renderFile('partials/question_summary', ['number' => $index + 1, 'item' => $item, 'withChart' => true]) ?>
        <?php endforeach; ?>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header">Respondents</div>
    <?php if ($responses === []): ?>
        <div class="card-body text-body-secondary">No responses yet.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Gender</th>
                    <th scope="col" class="text-end">Age</th>
                    <th scope="col">Education</th>
                    <th scope="col">Occupation</th>
                    <th scope="col">Institution</th>
                    <th scope="col">Submitted</th>
                    <th scope="col"><span class="visually-hidden">Actions</span></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($responses as $index => $response): ?>
                    <tr>
                        <td><?= e($index + 1) ?></td>
                        <td><?= e($response['respondent_name']) ?></td>
                        <td><?= e(Gender::from($response['gender'])->label()) ?></td>
                        <td class="text-end"><?= e($response['age']) ?></td>
                        <td><?= e(Education::from($response['education'])->label()) ?></td>
                        <td><?= e(Occupation::describe($response['occupation'], $response['occupation_other'])) ?></td>
                        <td><?= e($response['institution'] ?? '-') ?></td>
                        <td class="text-nowrap"><?= e(format_datetime($response['submitted_at'])) ?></td>
                        <td><a class="btn btn-sm btn-outline-primary" href="<?= e(url("{$prefix}/responses/{$response['id']}")) ?>">Answers</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
