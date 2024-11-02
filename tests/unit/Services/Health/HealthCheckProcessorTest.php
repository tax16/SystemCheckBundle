<?php

namespace unit\Services\Health;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Tax16\SystemCheckBundle\Core\Application\Service\HealthCheckProcessor;
use Tax16\SystemCheckBundle\Core\Domain\Enum\CriticalityLevel;
use Tax16\SystemCheckBundle\Core\Domain\Model\CheckInfo;
use Tax16\SystemCheckBundle\Core\Domain\Model\HealthCheck;
use Tax16\SystemCheckBundle\Core\Domain\Service\ServiceCheckInterface;

class HealthCheckProcessorTest extends TestCase
{
    private $processor;
    private $mockHealthChecks;

    protected function setUp(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);

        $this->mockHealthChecks = [
            [
                'service' => $this->createMock(ServiceCheckInterface::class),
                'label' => 'Check 1',
                'id' => 'check_1',
                'description' => 'Description 1',
                'priority' => CriticalityLevel::HIGH,
            ],
            [
                'service' => $this->createMock(ServiceCheckInterface::class),
                'label' => 'Check 2',
                'id' => 'check_2',
                'description' => 'Description 2',
                'priority' => CriticalityLevel::LOW,
            ],
        ];

        // Instantiate the HealthCheckProcessor with mocked dependencies
        $this->processor = new HealthCheckProcessor($this->mockHealthChecks, $loggerMock);
    }

    public function testPerformChecksReturnsCachedResults(): void
    {
        // Set up mocked service results
        $check1Result = $this->createMock(CheckInfo::class);
        $check1Result->method('isSuccess')->willReturn(true);

        $check2Result = $this->createMock(CheckInfo::class);
        $check2Result->method('isSuccess')->willReturn(false);

        // Define the return values for the service checks
        $this->mockHealthChecks[0]['service']->method('check')->willReturn($check1Result);
        $this->mockHealthChecks[1]['service']->method('check')->willReturn($check2Result);

        // Perform the checks for the first time
        $resultsFirstRun = $this->processor->performChecks();
        $this->assertCount(2, $resultsFirstRun);
        $this->assertInstanceOf(HealthCheck::class, $resultsFirstRun[0]);
        $this->assertInstanceOf(HealthCheck::class, $resultsFirstRun[1]);

        // Perform the checks again (should return cached results)
        $resultsSecondRun = $this->processor->performChecks();

        // Assert that the results from the second run are the same as the first
        $this->assertSame($resultsFirstRun[0]->getLabel(), $resultsSecondRun[0]->getLabel());
    }

    public function testPerformChecksCachesResults(): void
    {
        // Set up mocked service results
        $checkResult = $this->createMock(CheckInfo::class);
        $checkResult->method('isSuccess')->willReturn(true);

        // Define the return values for the service checks
        foreach ($this->mockHealthChecks as $mockHealthCheck) {
            $mockHealthCheck['service']->method('check')->willReturn($checkResult);
        }

        // Perform the checks
        $results = $this->processor->performChecks();

        // Clear the cache
        $this->processor->clearCache();

        // Perform the checks again (should not be cached this time)
        $resultsAfterClearCache = $this->processor->performChecks();

        // Assert that the results are still the same
        $this->assertCount(2, $resultsAfterClearCache);
        $this->assertNotSame($results, $resultsAfterClearCache); // Should not be the same reference, but values could be equal
    }

    public function testClearCacheResetsCachedResults(): void
    {
        // Set up mocked service results
        $checkResult = $this->createMock(CheckInfo::class);
        $checkResult->method('isSuccess')->willReturn(true);

        // Define the return values for the service checks
        foreach ($this->mockHealthChecks as $mockHealthCheck) {
            $mockHealthCheck['service']->method('check')->willReturn($checkResult);
        }

        // Perform the checks
        $resultsFirstRun = $this->processor->performChecks();

        // Clear the cache
        $this->processor->clearCache();

        // Perform the checks again (should not use cached results)
        $resultsSecondRun = $this->processor->performChecks();

        // Assert that the two results are the same but not the same instance
        $this->assertCount(2, $resultsSecondRun);
        $this->assertNotSame($resultsFirstRun, $resultsSecondRun);
    }
}