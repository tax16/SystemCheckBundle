<?php

namespace unit\Core\Application\Service;

use PHPUnit\Framework\TestCase;
use Tax16\SystemCheckBundle\Core\Application\Service\HealthCheckProcessor;
use Tax16\SystemCheckBundle\Core\Domain\Enum\CriticalityLevel;
use Tax16\SystemCheckBundle\Core\Domain\Model\CheckInfo;
use Tax16\SystemCheckBundle\Core\Domain\Model\HealthCheck;
use Tax16\SystemCheckBundle\Core\Domain\Port\ApplicationLoggerInterface;
use Tax16\SystemCheckBundle\Core\Domain\Service\ServiceCheckInterface;

class HealthCheckProcessorTest extends TestCase
{
    /**
     * @var HealthCheckProcessor
     */
    private $processor;

    /**
     * @var mixed[]
     */
    private $mockHealthChecks;

    protected function setUp(): void
    {
        $loggerMock = $this->createMock(ApplicationLoggerInterface::class);

        $this->mockHealthChecks = [
            [
                'service' => $this->createMock(ServiceCheckInterface::class),
                'label' => 'Check 1',
                'id' => 'check_1',
                'description' => 'Description 1',
                'priority' => CriticalityLevel::HIGH,
                'parent' => null,
                'execute' => true
            ],
            [
                'service' => $this->createMock(ServiceCheckInterface::class),
                'label' => 'Check 2',
                'id' => 'check_2',
                'description' => 'Description 2',
                'priority' => CriticalityLevel::LOW,
                'parent' => null,
                'execute' => true
            ],
        ];

        $this->processor = new HealthCheckProcessor($this->mockHealthChecks, $loggerMock);
    }

    public function testprocessReturnsCachedResults(): void
    {
        $check1Result = $this->createMock(CheckInfo::class);
        $check1Result->method('isSuccess')->willReturn(true);

        $check2Result = $this->createMock(CheckInfo::class);
        $check2Result->method('isSuccess')->willReturn(false);

        $this->mockHealthChecks[0]['service']->method('check')->willReturn($check1Result);
        $this->mockHealthChecks[1]['service']->method('check')->willReturn($check2Result);

        $resultsFirstRun = $this->processor->process();
        $this->assertCount(2, $resultsFirstRun);
        $this->assertInstanceOf(HealthCheck::class, $resultsFirstRun[0]);
        $this->assertInstanceOf(HealthCheck::class, $resultsFirstRun[1]);

        $resultsSecondRun = $this->processor->process();

        $this->assertSame($resultsFirstRun[0]->getLabel(), $resultsSecondRun[0]->getLabel());
    }

    public function testprocessCachesResults(): void
    {
        $checkResult = $this->createMock(CheckInfo::class);
        $checkResult->method('isSuccess')->willReturn(true);

        foreach ($this->mockHealthChecks as $mockHealthCheck) {
            $mockHealthCheck['service']->method('check')->willReturn($checkResult);
        }

        $results = $this->processor->process();

        $this->processor->clearCache();

        $resultsAfterClearCache = $this->processor->process();

        $this->assertCount(2, $resultsAfterClearCache);
        $this->assertNotSame($results, $resultsAfterClearCache);
    }

    public function testClearCacheResetsCachedResults(): void
    {
        $checkResult = $this->createMock(CheckInfo::class);
        $checkResult->method('isSuccess')->willReturn(true);

        foreach ($this->mockHealthChecks as $mockHealthCheck) {
            $mockHealthCheck['service']->method('check')->willReturn($checkResult);
        }

        $resultsFirstRun = $this->processor->process();

        $this->processor->clearCache();

        $resultsSecondRun = $this->processor->process();

        $this->assertCount(2, $resultsSecondRun);
        $this->assertNotSame($resultsFirstRun, $resultsSecondRun);
    }
}