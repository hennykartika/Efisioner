<?php

declare(strict_types=1);

namespace App\Domain;

final class Survey
{
    public function __construct(
        public readonly int $id,
        public readonly int $ownerId,
        public readonly string $title,
        public readonly string $description,
        public readonly int $targetRespondents,
        public readonly int $rewardPerRespondent,
        public readonly int $remainingSlots,
        public readonly ?string $paidAt,
        public readonly ?string $closedAt,
        public readonly string $createdAt,
    ) {
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            ownerId: (int) $row['owner_id'],
            title: (string) $row['title'],
            description: (string) $row['description'],
            targetRespondents: (int) $row['target_respondents'],
            rewardPerRespondent: (int) $row['reward_per_respondent'],
            remainingSlots: (int) $row['remaining_slots'],
            paidAt: $row['paid_at'] ?? null,
            closedAt: $row['closed_at'] ?? null,
            createdAt: (string) $row['created_at'],
        );
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->ownerId === $user->id;
    }

    public function isPaid(): bool
    {
        return $this->paidAt !== null;
    }

    public function isClosed(): bool
    {
        return $this->closedAt !== null;
    }

    public function acceptsResponses(): bool
    {
        return $this->isPaid() && !$this->isClosed() && $this->remainingSlots > 0;
    }

    public function collectedResponses(): int
    {
        return $this->isPaid() ? $this->targetRespondents - $this->remainingSlots : 0;
    }

    /**
     * Target and reward decide the price, so they are frozen once paid.
     */
    public function canChangePricing(): bool
    {
        return !$this->isPaid();
    }

    /**
     * Title, description and questions stay editable until the first response
     * arrives; after that, edits would make earlier answers meaningless.
     */
    public function canChangeContent(): bool
    {
        return !$this->isClosed() && $this->collectedResponses() === 0;
    }

    public function status(): SurveyStatus
    {
        return match (true) {
            !$this->isPaid() => SurveyStatus::Draft,
            $this->isClosed() => SurveyStatus::Closed,
            $this->remainingSlots === 0 => SurveyStatus::Completed,
            default => SurveyStatus::Active,
        };
    }
}
