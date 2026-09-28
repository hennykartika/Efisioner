<?php

declare(strict_types=1);

namespace App\Service;

final class SurveyPricing
{
    public const MIN_RESPONDENTS = 1;
    public const MAX_RESPONDENTS = 5000;
    public const MIN_REWARD = 1000;
    public const MAX_REWARD = 1000000;

    public function __construct(private readonly int $platformFee)
    {
    }

    public function platformFee(): int
    {
        return $this->platformFee;
    }

    public function total(int $respondents, int $rewardPerRespondent): int
    {
        return $respondents * $rewardPerRespondent + $this->platformFee;
    }
}
