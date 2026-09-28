<?php
/**
 * Text input or textarea with label and validation feedback.
 *
 * @var string $name
 * @var string $label
 * @var string|null $value
 * @var string|null $error
 * @var string|null $type
 * @var string|null $help
 * @var array<string, string|int|bool|null>|null $attributes
 */
$type ??= 'text';
$value ??= '';
$error ??= null;
$help ??= null;
$attributes ??= [];
$id = 'field-' . str_replace('_', '-', $name);
$common = [
    'id' => $id,
    'name' => $name,
    'class' => 'form-control' . ($error !== null ? ' is-invalid' : ''),
    'aria-describedby' => $help !== null ? $id . '-help' : null,
    ...$attributes,
];
?>
<div class="mb-3">
    <label for="<?= e($id) ?>" class="form-label"><?= e($label) ?></label>
    <?php if ($type === 'textarea'): ?>
        <textarea<?= html_attributes($common) ?>><?= e($value) ?></textarea>
    <?php else: ?>
        <input<?= html_attributes(['type' => $type, 'value' => $type === 'password' ? null : $value, ...$common]) ?>>
    <?php endif; ?>
    <?php if ($error !== null): ?>
        <div class="invalid-feedback"><?= e($error) ?></div>
    <?php endif; ?>
    <?php if ($help !== null): ?>
        <div class="form-text" id="<?= e($id) ?>-help"><?= e($help) ?></div>
    <?php endif; ?>
</div>
