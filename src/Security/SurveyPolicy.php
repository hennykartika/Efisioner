<?php

declare(strict_types=1);

namespace App\Security;

use App\Domain\Survey;
use App\Domain\User;
use App\Http\HttpException;
use App\Repository\SurveyRepository;

/**
 * Loads a survey only if the user is allowed to touch it. Missing and
 * foreign surveys both answer 404 so record ids cannot be probed.
 */
final class SurveyPolicy
{
    public function __construct(private readonly SurveyRepository $surveys)
    {
    }

    public function owned(User $user, int $surveyId): Survey
    {
        $survey = $this->surveys->find($surveyId);

        if ($survey === null || !$survey->isOwnedBy($user)) {
            throw HttpException::notFound();
        }

        return $survey;
    }

    /**
     * Results contain respondents' personal data: owner and admins only.
     */
    public function results(User $user, int $surveyId): Survey
    {
        $survey = $this->surveys->find($surveyId);

        if ($survey === null || (!$survey->isOwnedBy($user) && !$user->isAdmin())) {
            throw HttpException::notFound();
        }

        return $survey;
    }
}
