<?php

namespace unit\Services\Health\Decorator;


use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tax16\SystemCheckBundle\Services\Health\Checker\Decorator\ServiceCheckDecorator;
use Tax16\SystemCheckBundle\Services\Health\Checker\ServiceCheckInterface;
use Tax16\SystemCheckBundle\DTO\CheckResult;
use Tax16\SystemCheckBundle\DTO\EavDTO;

class ServiceCheckDecoratorTest extends TestCase
{
    private ServiceCheckInterface|MockObject $mockService;
    private ServiceCheckDecorator $decorator;

    protected function setUp(): void
    {
        $this->mockService = $this->createMock(ServiceCheckInterface::class);
        $this->decorator = new ServiceCheckDecorator($this->mockService);
    }

    public function testCheckMeasuresDurationMemoryAndCpuLoad(): void
    {
        $mockResult = $this->createMock(CheckResult::class);
        $this->mockService->method('check')->willReturn($mockResult);

        $mockResult->expects($this->exactly(3))
            ->method('addEav')
            ->with($this->isInstanceOf(EavDTO::class));

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