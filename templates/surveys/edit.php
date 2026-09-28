<?php
/**
 * @var App\View\View $view
 * @var App\Security\Csrf $csrf
 * @var App\Domain\Survey $survey
 * @var array<string, string> $old
 * @var array<string, string> $errors
 * @var list<App\Domain\Question> $questions
 * @var array<string, mixed>|null $payment latest payment attempt
 * @var int $platformFee
 * @var int $total
 */
$view->set('title', $survey->title);
$status = $survey->status();
$canEditContent = $survey->canChangeContent();
$canEditAnything = $canEditContent || $survey->canChangePricing();
$base = "/my-surveys/{$survey->id}";
?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= e(url('/my-surveys')) ?>">My surveys</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= e($survey->title) ?></li>
    </ol>
</nav>

<div class="d-flex flex-wrap align-items-center gap-2 mb-4">
    <h1 class="h3 mb-0 me-2"><?= e($survey->title) ?></h1>
    <span class="badge <?= e($status->badgeClass()) ?>"><?= e($status->label()) ?></span>
    <?php if ($survey->isPaid()): ?>
        <a class="btn btn-outline-primary btn-sm ms-auto" href="<?= e(url($base . '/results')) ?>">View results</a>
    <?php endif; ?>
</div>

<?php if ($survey->isPaid()): ?>
    <div class="alert alert-info">
        Published on <?= e(format_datetime($survey->paidAt)) ?>.
        <?= e($survey->collectedResponses()) ?> of <?= e($survey->targetRespondents) ?> responses collected.
        <?php if (!$canEditContent): ?>
            The survey is locked because respondents have already answered it.
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card shadow-sm mb-4">
            <div class="card-header">Details</div>
            <div class="card-body">
                <form method="post" action="<?= e(url($base . '/edit')) ?>">
                    <input type="hidden" name="_token" value="<?= e($csrf->token()) ?>">
                    <?= $view->renderFile('partials/survey_fields', [
                        'old' => $old,
                        'errors' => $errors,
                        'platformFee' => $platformFee,
                        'contentLocked' => !$canEditContent,
                        'pricingLocked' => !$survey->canChangePricing(),
                    ]) ?>
                    <?php if ($canEditAnything): ?>
                        <button type="submit" class="btn btn-brand">Save details</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Questions (<?= e(count($questions)) ?>)</span>
                <?php if ($canEditContent): ?>
                    <a class="btn btn-sm btn-brand" href="<?= e(url($base . '/questions/new')) ?>">Add question</a>
                <?php endif; ?>
            </div>
            <?php if ($questions === []): ?>
                <div class="card-body text-body-secondary">No questions yet. Add at least one before paying.</div>
            <?php else: ?>
                <ol class="list-group list-group-flush list-group-numbered">
                    <?php foreach ($questions as $question): ?>
                        <li class="list-group-item d-flex gap-3">
                            <div class="flex-grow-1">
                                <div class="fw-semibold"><?= e($question->body) ?></div>
                                <ul class="list-unstyled small text-body-secondary mb-0">
                                    <?php foreach ($question->options() as $letter => $text): ?>
                                        <li><?= e($letter) ?>. <?= e($text) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php if ($canEditContent): ?>
                                <div class="text-nowrap">
                                    <a class="btn btn-sm btn-outline-secondary" href="<?= e(url("{$base}/questions/{$question->id}/edit")) ?>">Edit</a>
                                    <form method="post" action="<?= e(url("{$base}/questions/{$question->id}/delete")) ?>" class="d-inline" data-confirm="Delete this question?">
                                        <input type="hidden" name="_token" value="<?= e($csrf->token()) ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!$survey->isPaid()): ?>
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header">Payment</div>
                <div class="card-body">
                    <dl class="row small mb-3">
                        <dt class="col-7 fw-normal">Rewards (<?= e($survey->targetRespondents) ?> &times; <?= e(rupiah($survey->rewardPerRespondent)) ?>)</dt>
                        <dd class="col-5 text-end"><?= e(rupiah($survey->targetRespondents * $survey->rewardPerRespondent)) ?></dd>
                        <dt class="col-7 fw-normal">Platform fee</dt>
                        <dd class="col-5 text-end"><?= e(rupiah($platformFee)) ?></dd>
                        <dt class="col-7">Total</dt>
                        <dd class="col-5 text-end fw-semibold"><?= e(rupiah($total)) ?></dd>
                    </dl>

                    <?php if ($payment !== null && $payment['status'] === 'pending'): ?>
                        <div class="alert alert-warning small">
                            A payment started on <?= e(format_datetime($payment['created_at'])) ?> is still waiting for confirmation.
                            <a href="<?= e(url($base . '/payment/finish')) ?>">Check status</a>
                        </div>
                    <?php elseif ($payment !== null && $payment['status'] === 'failed'): ?>
                        <div class="alert alert-danger small">The last payment attempt did not go through. You can try again.</div>
                    <?php endif; ?>

                    <form method="post" action="<?= e(url($base . '/checkout')) ?>">
                        <input type="hidden" name="_token" value="<?= e($csrf->token()) ?>">
                        <button type="submit" class="btn btn-success w-100"<?= $questions === [] ? ' disabled' : '' ?>>Pay and publish</button>
                    </form>
                    <p class="small text-body-secondary mt-3 mb-0">
                        Payment is handled by Midtrans. The survey goes live as soon as the payment is confirmed.
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
