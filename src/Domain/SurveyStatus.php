<?php

declare(strict_types=1);

namespace App\Domain;

enum SurveyStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Completed = 'completed';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Active => 'Active',
            self::Completed => 'Completed',
            self::Closed => 'Closed',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'text-bg-secondary',
            self::Active => 'text-bg-success',
            self::Completed => 'text-bg-primary',
            self::Closed => 'text-bg-dark',
        };
    }
}
