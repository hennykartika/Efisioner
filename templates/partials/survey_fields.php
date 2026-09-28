<?php
/**
 * @var App\View\View $view
 * @var array<string, string> $old
 * @var array<string, string> $errors
 * @var int $platformFee
 * @var bool|null $contentLocked
 * @var bool|null $pricingLocked
 */
use App\Service\SurveyPricing;

$contentLocked ??= false;
$pricingLocked ??= false;
$field = static fn (string $name, string $label, array $extra = []): string => $view->renderFile('partials/field', [
    'name' => $name,
    'label' => $label,
    'value' => $old[$name] ?? '',
    'error' => $errors[$name] ?? null,
    ...$extra,
]);
?>
<?= $field('title', 'Title', ['attributes' => ['required' => true, 'maxlength' => 150, 'disabled' => $contentLocked]]) ?>
<?= $field('description', 'Description', [
    'type' => 'textarea',
    'help' => 'Explain the purpose of the research, who should answer and how long it takes.',
    'attributes' => ['required' => true, 'rows' => 5, 'maxlength' => 2000, 'disabled' => $contentLocked],
]) ?>

<div class="row" data-price-calculator data-fee="<?= e($platformFee) ?>">
    <div class="col-md-6">
        <?= $field('target_respondents', 'Target respondents', [
            'type' => 'number',
            'attributes' => [
                'required' => true,
                'min' => SurveyPricing::MIN_RESPONDENTS,
                'max' => SurveyPricing::MAX_RESPONDENTS,
                'step' => 1,
                'disabled' => $pricingLocked,
                'data-price-count' => true,
            ],
        ]) ?>
    </div>
    <div class="col-md-6">
        <?= $field('reward_per_respondent', 'Reward per respondent (IDR)', [
            'type' => 'number',
            'help' => sprintf('Between %s and %s.', rupiah(SurveyPricing::MIN_REWARD), rupiah(SurveyPricing::MAX_REWARD)),
            'attributes' => [
                'required' => true,
                'min' => SurveyPricing::MIN_REWARD,
                'max' => SurveyPricing::MAX_REWARD,
                'step' => 1,
                'disabled' => $pricingLocked,
                'data-price-reward' => true,
            ],
        ]) ?>
    </div>
    <div class="col-12">
        <p class="mb-3">
            Total to pay: <strong data-price-total>-</strong>
            <span class="small text-body-secondary">(rewards plus <?= e(rupiah($platformFee)) ?> platform fee)</span>
        </p>
    </div>
</div>
