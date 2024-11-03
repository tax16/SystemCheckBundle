<?php

declare(strict_types=1);

namespace unit\Core\Application\Helper;

use PHPUnit\Framework\TestCase;
use Tax16\SystemCheckBundle\Core\Application\Helper\HealthCheckHelper;
use Tax16\SystemCheckBundle\Core\Domain\Model\CheckInfo;
use Tax16\SystemCheckBundle\Core\Domain\Model\HealthCheck;

class HealthCheckHelperTest extends TestCase
{
    public function testCountHealthChecksWithNoChecks(): void
    {
        $healthChecks = [];
        $this->assertEquals(0, HealthCheckHelper::countHealthChecks($healthChecks));
    }

    public function testCountHealthChecksWithSingleCheck(): void
    {
        $healthCheck = $this->createMock(HealthCheck::class);
        $result = $this->createMock(CheckInfo::class);

        $healthCheck->method('getResult')->willReturn($result);
        $result->method('getChildren')->willReturn([]);

        $this->assertEquals(1, HealthCheckHelper::countHealthChecks([$healthCheck]));
    }

    public function testCountHealthChecksWithNestedChecks(): void
    {
        $childHealthCheck = $this->createMock(HealthCheck::class);
        $childResult = $this->createMock(CheckInfo::class);
        $childResult->method('getChildren')->willReturn([]);
        $childHealthCheck->method('getResult')->willReturn($childResult);

        $parentHealthCheck = $this->createMock(HealthCheck::class);
        $parentResult = $this->createMock(CheckInfo::class);
        $parentResult->method('getChildren')->willReturn([$childHealthCheck]);
        $parentHealthCheck->method('getResult')->willReturn($parentResult);

        $this->assertEquals(2, HealthCheckHelper::countHealthChecks([$parentHealthCheck]));
    }

    public function testListAllHealthChecksWithNoChecks(): void
    {
        $healthChecks = [];
        $this->assertEquals([], HealthCheckHelper::listAllHealthChecks($healthChecks));
    }

    public function testListAllHealthChecksWithSingleCheck(): void
    {
        $healthCheck = $this->createMock(HealthCheck::class);
        $result = $this->createMock(CheckInfo::class);

        $healthCheck->method('getResult')->willReturn($result);
        $result->method('getChildren')->willReturn([]);

        $this->assertEquals([$healthCheck], HealthCheckHelper::listAllHealthChecks([$healthCheck]));
    }

    public function testListAllHealthChecksWithNestedChecks(): void
    {
        $childHealthCheck = $this->createMock(HealthCheck::class);
        $childResult = $this->createMock(CheckInfo::class);
        $childResult->method('getChildren')->willReturn([]);
        $childHealthCheck->method('getResult')->willReturn($childResult);

        $parentHealthCheck = $this->createMock(HealthCheck::class);
        $parentResult = $this->createMock(CheckInfo::class);
        $parentResult->method('getChildren')->willReturn([$childHealthCheck]);
        $parentHealthCheck->method('getResult')->willReturn($parentResult);

        $this->assertEquals([$parentHealthCheck, $childHealthCheck], HealthCheckHelper::listAllHealthChecks([$parentHealthCheck]));
    }
}
