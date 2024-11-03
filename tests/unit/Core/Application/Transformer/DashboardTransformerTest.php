<?php

namespace unit\Core\Application\Transformer;

use PHPUnit\Framework\TestCase;
use Tax16\SystemCheckBundle\Core\Application\DTO\HealthCheckCategory;
use Tax16\SystemCheckBundle\Core\Application\Transformer\DashboardTransformer;
use Tax16\SystemCheckBundle\Core\Domain\Enum\CriticalityLevel;
use Tax16\SystemCheckBundle\Core\Domain\Model\CheckInfo;
use Tax16\SystemCheckBundle\Core\Domain\Model\HealthCheck;

class DashboardTransformerTest extends TestCase
{
    private $transformer;

    protected function setUp(): void
    {
        $this->transformer = new DashboardTransformer();
    }

    public function testTransformWithAllSuccessChecks(): void
    {
        $results = [
            new HealthCheck(new CheckInfo("Check 1", true), "fake_id_1", "Label 1", "Description 1", CriticalityLevel::HIGH),
            new HealthCheck(new CheckInfo("Check 2", true),"fake_id_2",  "Label 2", "Description 2", CriticalityLevel::MEDIUM),
        ];

        $result = $this->transformer->transform($results);

        $this->assertInstanceOf(HealthCheckCategory::class, $result);
        $this->assertCount(2, $result->getSuccessChecks());
        $this->assertCount(0, $result->getFailedChecks());
        $this->assertCount(0, $result->getWarningChecks());
    }

    public function testTransformWithMixedChecks(): void
    {
        $results = [
            new HealthCheck(new CheckInfo("Check 1", true), "fake_id_1", "Label 1", "Description 1", CriticalityLevel::HIGH),
            new HealthCheck(new CheckInfo("Check 2", false), "fake_id_2", "Label 2", "Description 2", CriticalityLevel::HIGH), // failed
            new HealthCheck(new CheckInfo("Check 3", false), "fake_id_3", "Label 3", "Description 3", CriticalityLevel::LOW), // warning
        ];

        $result = $this->transformer->transform($results);

        $this->assertCount(1, $result->getSuccessChecks());
        $this->assertCount(1, $result->getFailedChecks());
        $this->assertCount(1, $result->getWarningChecks());
    }

    public function testTransformWithEmptyArray(): void
    {
        $results = [];

        $result = $this->transformer->transform($results);

        $this->assertInstanceOf(HealthCheckCategory::class, $result);
        $this->assertCount(0, $result->getSuccessChecks());
        $this->assertCount(0, $result->getFailedChecks());
        $this->assertCount(0, $result->getWarningChecks());
    }

    public function testTransformWithAllFailedChecks(): void
    {
        $results = [
            new HealthCheck(new CheckInfo("Check 1", false), "fake_id_1", "Label 1", "Description 1", CriticalityLevel::HIGH),
            new HealthCheck(new CheckInfo("Check 2", false), "fake_id_2", "Label 2", "Description 2", CriticalityLevel::LOW), // warning
        ];

        $result = $this->transformer->transform($results);

        $this->assertCount(0, $result->getSuccessChecks());
        $this->assertCount(1, $result->getFailedChecks());
        $this->assertCount(1, $result->getWarningChecks());
    }

    public function testTransformWithMixedPriorities(): void
    {
        $results = [
            new HealthCheck(new CheckInfo("Check 1", true), "fake_id_1", "Label 1", "Description 1", CriticalityLevel::HIGH),
            new HealthCheck(new CheckInfo("Check 2", false), "fake_id_2", "Label 2", "Description 2", CriticalityLevel::HIGH), // failed
            new HealthCheck(new CheckInfo("Check 3", false), "fake_id_3", "Label 3", "Description 3", CriticalityLevel::LOW), // warning
            new HealthCheck(new CheckInfo("Check 4", true), "fake_id_4", "Label 4", "Description 4", CriticalityLevel::MEDIUM),
        ];

        $result = $this->transformer->transform($results);

        $this->assertCount(2, $result->getSuccessChecks());
        $this->assertCount(1, $result->getFailedChecks());
        $this->assertCount(1, $result->getWarningChecks());
    }
}