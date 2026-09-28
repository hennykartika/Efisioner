<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Survey;
use App\Domain\SurveyStatus;
use App\Service\SurveyPricing;
use PHPUnit\Framework\TestCase;

final class SurveyTest extends TestCase
{
    public function testDraftCanBeEditedButNotAnswered(): void
    {
        $survey = $this->survey();

        $this->assertSame(SurveyStatus::Draft, $survey->status());
        $this->assertTrue($survey->canChangePricing());
        $this->assertTrue($survey->canChangeContent());
        $this->assertFalse($survey->acceptsResponses());
        $this->assertSame(0, $survey->collectedResponses());
    }

    public function testPaidSurveyLocksPricingButNotContentUntilFirstResponse(): void
    {
        $survey = $this->survey(paidAt: '2026-01-01 10:00:00');

        $this->assertSame(SurveyStatus::Active, $survey->status());
        $this->assertFalse($survey->canChangePricing());
        $this->assertTrue($survey->canChangeContent());
        $this->assertTrue($survey->acceptsResponses());
    }

    public function testFirstResponseLocksContent(): void
    {
        $survey = $this->survey(remaining: 9, paidAt: '2026-01-01 10:00:00');

        $this->assertSame(1, $survey->collectedResponses());
        $this->assertFalse($survey->canChangeContent());
    }

    public function testCompletedAndClosedSurveysStopAcceptingResponses(): void
    {
        $completed = $this->survey(remaining: 0, paidAt: '2026-01-01 10:00:00');
        $closed = $this->survey(paidAt: '2026-01-01 10:00:00', closedAt: '2026-01-02 10:00:00');

        $this->assertSame(SurveyStatus::Completed, $completed->status());
        $this->assertFalse($completed->acceptsResponses());
        $this->assertSame(SurveyStatus::Closed, $closed->status());
        $this->assertFalse($closed->acceptsResponses());
    }

    public function testPriceIncludesPlatformFee(): void
    {
        $this->assertSame(51000, (new SurveyPricing(1000))->total(10, 5000));
    }

    private function survey(int $remaining = 10, ?string $paidAt = null, ?string $closedAt = null): Survey
    {
        return new Survey(1, 7, 'Title', 'Description', 10, 5000, $remaining, $paidAt, $closedAt, '2026-01-01 09:00:00');
    }
}
