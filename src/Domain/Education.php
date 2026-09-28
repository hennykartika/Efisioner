<?php

declare(strict_types=1);

namespace App\Domain;

enum Education: string
{
    case Primary = 'primary';
    case JuniorHigh = 'junior_high';
    case SeniorHigh = 'senior_high';
    case Diploma3 = 'diploma_3';
    case Diploma4 = 'diploma_4';
    case Bachelor = 'bachelor';
    case Master = 'master';
    case Doctorate = 'doctorate';

    public function label(): string
    {
        return match ($this) {
            self::Primary => 'Primary school',
            self::JuniorHigh => 'Junior high school',
            self::SeniorHigh => 'Senior high school',
            self::Diploma3 => 'Diploma III',
            self::Diploma4 => 'Diploma IV',
            self::Bachelor => "Bachelor's degree",
            self::Master => "Master's degree",
            self::Doctorate => 'Doctorate',
        };
    }
}
