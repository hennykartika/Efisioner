<?php

declare(strict_types=1);

namespace App\Domain;

enum Occupation: string
{
    case CivilServant = 'civil_servant';
    case Military = 'military';
    case Police = 'police';
    case PrivateSector = 'private_sector';
    case SelfEmployed = 'self_employed';
    case Student = 'student';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::CivilServant => 'Civil servant',
            self::Military => 'Military',
            self::Police => 'Police',
            self::PrivateSector => 'Private sector employee',
            self::SelfEmployed => 'Self-employed',
            self::Student => 'Student',
            self::Other => 'Other',
        };
    }

    public static function describe(string $value, ?string $other): string
    {
        $occupation = self::tryFrom($value);

        if ($occupation === self::Other && $other !== null && $other !== '') {
            return $other;
        }

        return $occupation?->label() ?? $value;
    }
}
