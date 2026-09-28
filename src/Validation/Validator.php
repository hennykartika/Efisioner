<?php

declare(strict_types=1);

namespace App\Validation;

use BackedEnum;
use DateTimeImmutable;

/**
 * Collects the first error per field. Input is normalised on construction:
 * strings are trimmed and stripped of control characters (tabs and line breaks
 * are kept for multi-line fields).
 */
final class Validator
{
    /** @var array<string, string> */
    private array $data;

    /** @var array<string, string> */
    private array $errors = [];

    /**
     * @param array<string, string> $input
     */
    public function __construct(array $input)
    {
        $this->data = array_map(
            static fn (string $value): string => trim((string) preg_replace('/[^\P{C}\t\r\n]/u', '', $value)),
            $input,
        );
    }

    public function value(string $field): string
    {
        return $this->data[$field] ?? '';
    }

    public function nullable(string $field): ?string
    {
        $value = $this->value($field);

        return $value === '' ? null : $value;
    }

    public function required(string $field, string $label): self
    {
        if ($this->value($field) === '') {
            $this->addError($field, sprintf('%s is required.', $label));
        }

        return $this;
    }

    public function length(string $field, string $label, int $min, int $max): self
    {
        $value = $this->value($field);
        if ($value === '') {
            return $this;
        }

        $length = mb_strlen($value);
        if ($length < $min) {
            $this->addError($field, sprintf('%s must be at least %d characters.', $label, $min));
        } elseif ($length > $max) {
            $this->addError($field, sprintf('%s must be at most %d characters.', $label, $max));
        }

        return $this;
    }

    public function pattern(string $field, string $regex, string $message): self
    {
        $value = $this->value($field);
        if ($value !== '' && preg_match($regex, $value) !== 1) {
            $this->addError($field, $message);
        }

        return $this;
    }

    public function email(string $field, string $label): self
    {
        $value = $this->value($field);
        if ($value !== '' && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $this->addError($field, sprintf('%s must be a valid email address.', $label));
        }

        return $this;
    }

    public function integer(string $field, string $label, int $min, int $max): self
    {
        $value = $this->value($field);
        if ($value === '') {
            return $this;
        }

        $number = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => $min, 'max_range' => $max]]);
        if ($number === false) {
            $this->addError($field, sprintf(
                '%s must be a whole number between %s and %s.',
                $label,
                number_format($min, 0, ',', '.'),
                number_format($max, 0, ',', '.'),
            ));
        }

        return $this;
    }

    /**
     * @param class-string<BackedEnum> $enum
     */
    public function enum(string $field, string $label, string $enum): self
    {
        $value = $this->value($field);
        if ($value !== '' && $enum::tryFrom($value) === null) {
            $this->addError($field, sprintf('Please choose a valid %s.', mb_strtolower($label)));
        }

        return $this;
    }

    public function date(string $field, string $label): self
    {
        $value = $this->value($field);
        if ($value === '') {
            return $this;
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        if ($date === false || $date->format('Y-m-d') !== $value) {
            $this->addError($field, sprintf('%s must be a valid date.', $label));
        }

        return $this;
    }

    public function addError(string $field, string $message): void
    {
        $this->errors[$field] ??= $message;
    }

    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    public function passes(): bool
    {
        return $this->errors === [];
    }

    /**
     * @return array<string, string>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * @return array<string, string>
     */
    public function data(): array
    {
        return $this->data;
    }
}
