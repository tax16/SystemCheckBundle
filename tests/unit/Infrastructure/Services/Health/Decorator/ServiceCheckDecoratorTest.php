<?php

namespace unit\Infrastructure\Services\Health\Decorator;

use PHPUnit\Framework\TestCase;
use Tax16\SystemCheckBundle\Core\Domain\Model\CheckInfo;
use Tax16\SystemCheckBundle\Core\Domain\Model\Eav;
use Tax16\SystemCheckBundle\Core\Domain\Service\ServiceCheckInterface;
use Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker\Decorator\ServiceCheckDecorator;

class ServiceCheckDecoratorTest extends TestCase
{
    private $mockService;
    private $decorator;

    protected function setUp(): void
    {
        $this->mockService = $this->createMock(ServiceCheckInterface::class);
        $this->decorator = new ServiceCheckDecorator($this->mockService);
    }

    public function testCheckMeasuresDurationMemoryAndCpuLoad(): void
    {
        $mockResult = $this->createMock(CheckInfo::class);
        $this->mockService->method('check')->willReturn($mockResult);

        $mockResult->expects($this->exactly(3))
            ->method('addEav')
            ->with($this->isInstanceOf(Eav::class));

        $result = $this->decorator->check();

        $this->assertSame($mockResult, $result);
        $this->assertEquals($this->decorator->getName(), $this->mockService->getName());
    }

    public function testGetNameReturnsNameFromDecoratedService(): void
    {
        $this->mockService->method('getName')->willReturn('Mock Service');
        $this->assertEquals('Mock Service', $this->decorator->getName());
    }

    public function testGetIconReturnsIconFromDecoratedService(): void
    {
        $this->mockService->method('getIcon')->willReturn('icon.png');
        $this->assertEquals('icon.png', $this->decorator->getIcon());
    }
}