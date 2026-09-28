<?php

declare(strict_types=1);

namespace App\Domain;

final class Question
{
    public function __construct(
        public readonly int $id,
        public readonly int $surveyId,
        public readonly string $body,
        public readonly string $optionA,
        public readonly string $optionB,
        public readonly ?string $optionC,
        public readonly ?string $optionD,
    ) {
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            surveyId: (int) $row['survey_id'],
            body: (string) $row['body'],
            optionA: (string) $row['option_a'],
            optionB: (string) $row['option_b'],
            optionC: $row['option_c'] ?? null,
            optionD: $row['option_d'] ?? null,
        );
    }

    /**
     * Non-empty options keyed by their letter.
     *
     * @return array<string, string>
     */
    public function options(): array
    {
        return array_filter(
            ['A' => $this->optionA, 'B' => $this->optionB, 'C' => $this->optionC, 'D' => $this->optionD],
            static fn (?string $text): bool => $text !== null && $text !== '',
        );
    }

    public function hasOption(string $letter): bool
    {
        return array_key_exists($letter, $this->options());
    }
}
