<?php
/**
 * Personal details used by registration and profile editing.
 *
 * @var App\View\View $view
 * @var array<string, string> $old
 * @var array<string, string> $errors
 */
use App\Domain\Education;
use App\Domain\Gender;
use App\Domain\Occupation;

$old += array_fill_keys(App\Validation\ProfileForm::FIELDS, '');
$field = static fn (string $name, string $label, array $extra = []): string => $view->renderFile('partials/field', [
    'name' => $name,
    'label' => $label,
    'value' => $old[$name],
    'error' => $errors[$name] ?? null,
    ...$extra,
]);
$invalid = static fn (string $name): string => isset($errors[$name]) ? ' is-invalid' : '';
?>
<?= $field('full_name', 'Full name', ['attributes' => ['required' => true, 'maxlength' => 100, 'autocomplete' => 'name']]) ?>

<div class="row">
    <div class="col-md-6">
        <?= $field('email', 'Email', ['type' => 'email', 'attributes' => ['required' => true, 'maxlength' => 254, 'autocomplete' => 'email']]) ?>
    </div>
    <div class="col-md-6">
        <?= $field('phone', 'Phone number (optional)', ['type' => 'tel', 'attributes' => ['maxlength' => 20, 'autocomplete' => 'tel']]) ?>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <?= $field('birth_date', 'Date of birth', ['type' => 'date', 'attributes' => ['required' => true, 'max' => date('Y-m-d')]]) ?>
    </div>
    <div class="col-md-6 mb-3">
        <span class="form-label d-block">Gender</span>
        <?php foreach (Gender::cases() as $gender): ?>
            <div class="form-check form-check-inline">
                <input class="form-check-input<?= $invalid('gender') ?>" type="radio" name="gender" id="gender-<?= e($gender->value) ?>" value="<?= e($gender->value) ?>" required<?= $old['gender'] === $gender->value ? ' checked' : '' ?>>
                <label class="form-check-label" for="gender-<?= e($gender->value) ?>"><?= e($gender->label()) ?></label>
            </div>
        <?php endforeach; ?>
        <?php if (isset($errors['gender'])): ?>
            <div class="invalid-feedback d-block"><?= e($errors['gender']) ?></div>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="field-education" class="form-label">Highest education</label>
        <select id="field-education" name="education" class="form-select<?= $invalid('education') ?>" required>
            <option value="">Choose...</option>
            <?php foreach (Education::cases() as $education): ?>
                <option value="<?= e($education->value) ?>"<?= $old['education'] === $education->value ? ' selected' : '' ?>><?= e($education->label()) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['education'])): ?>
            <div class="invalid-feedback"><?= e($errors['education']) ?></div>
        <?php endif; ?>
    </div>
    <div class="col-md-6 mb-3">
        <label for="field-occupation" class="form-label">Occupation</label>
        <select id="field-occupation" name="occupation" class="form-select<?= $invalid('occupation') ?>" required data-other-toggle="#occupation-other">
            <option value="">Choose...</option>
            <?php foreach (Occupation::cases() as $occupation): ?>
                <option value="<?= e($occupation->value) ?>"<?= $old['occupation'] === $occupation->value ? ' selected' : '' ?>><?= e($occupation->label()) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['occupation'])): ?>
            <div class="invalid-feedback"><?= e($errors['occupation']) ?></div>
        <?php endif; ?>
    </div>
</div>

<div id="occupation-other"<?= $old['occupation'] === Occupation::Other->value ? '' : ' hidden' ?>>
    <?= $field('occupation_other', 'Please specify your occupation', ['attributes' => ['maxlength' => 100]]) ?>
</div>

<?= $field('institution', 'Institution or organisation (optional)', ['attributes' => ['maxlength' => 150, 'autocomplete' => 'organization']]) ?>
<?= $field('address', 'Address (optional)', ['type' => 'textarea', 'attributes' => ['rows' => 3, 'maxlength' => 500, 'autocomplete' => 'street-address']]) ?>
