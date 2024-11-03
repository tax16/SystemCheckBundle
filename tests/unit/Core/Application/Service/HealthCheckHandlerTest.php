<?php

namespace unit\Core\Application\Service;

use PHPUnit\Framework\TestCase;
use Tax16\SystemCheckBundle\Core\Application\DTO\HealthCheckCategory;
use Tax16\SystemCheckBundle\Core\Application\Service\HealthCheckHandler;
use Tax16\SystemCheckBundle\Core\Application\Service\HealthCheckProcessor;
use Tax16\SystemCheckBundle\Core\Application\Transformer\DashboardTransformer;
use Tax16\SystemCheckBundle\Core\Application\Transformer\NodeTransformer;
use Tax16\SystemCheckBundle\Core\Domain\Constant\CheckerIcon;
use Tax16\SystemCheckBundle\Core\Domain\Enum\CriticalityLevel;
use Tax16\SystemCheckBundle\Core\Domain\Model\CheckInfo;
use Tax16\SystemCheckBundle\Core\Domain\Model\HealthCheck;
use Tax16\SystemCheckBundle\Core\Domain\Port\ApplicationLoggerInterface;
use Tax16\SystemCheckBundle\Core\Domain\Port\ConfigurationProviderInterface;
use Tax16\SystemCheckBundle\Core\Domain\ValueObject\SystemNetwork;

class HealthCheckHandlerTest extends TestCase
{
    /**
     * @var HealthCheckHandler
     */
    private $handler;

    /**
     * @var ApplicationLoggerInterface
     */
    private $loggerMock;

    /**
     * @var HealthCheckProcessor
     */
    private $processorMock;

    /**
     * @var DashboardTransformer
     */
    private $dashboardTransformerMock;

    /**
     * @var NodeTransformer
     */
    private $nodeTransformerMock;

    /**
     * @var ConfigurationProviderInterface
     */
    private $parameterBagMock;

    protected function setUp(): void
    {
        $this->loggerMock = $this->createMock(ApplicationLoggerInterface::class);

        $this->processorMock = $this->createMock(HealthCheckProcessor::class);

        $this->dashboardTransformerMock = $this->createMock(DashboardTransformer::class);

        $this->nodeTransformerMock = $this->createMock(NodeTransformer::class);

        $this->parameterBagMock = $this->createMock(ConfigurationProviderInterface::class);
        $this->parameterBagMock->method('get')
            ->willReturnCallback(function ($key) {
                if ($key === 'system_check.name') {
                    return 'some_name';
                }
                if ($key === 'system_check.id') {
                    return 'some_id';
                }
                return 'some_value';
            });
        $this->handler = new HealthCheckHandler(
            $this->loggerMock,
            $this->processorMock,
            $this->dashboardTransformerMock,
            $this->nodeTransformerMock,
            $this->parameterBagMock
        );
    }

    public function testGetHealthCheckDashboardReturnsCorrectDTO(): void
    {
        $mockResults = [
            new HealthCheck(
                new CheckInfo('Check 1', true),
                'check_1',
                'Check 1',
                'Check 1 Description',
                CriticalityLevel::HIGH,
                CheckerIcon::UNKNOWN
            ),
            new HealthCheck(
                new CheckInfo('Check 2', false),
                'check_2',
                'Check 2',
                'Check 2 Description',
                CriticalityLevel::LOW,
                CheckerIcon::UNKNOWN
            ),
        ];

        $this->processorMock->method('process')->willReturn($mockResults);

        $mockDashboardDTO = new HealthCheckCategory([], [], []);
        $this->dashboardTransformerMock->method('transform')->willReturn($mockDashboardDTO);

        $result = $this->handler->getHealthCheckDashboard();

        $this->assertInstanceOf(HealthCheckCategory::class, $result);
        $this->assertSame($mockDashboardDTO, $result);
    }

    public function testGetHealthCheckResultReturnsCorrectArray(): void
    {
        $mockResults = [
            new HealthCheck(
                new CheckInfo('Check 1', true),
                'check_1',
                'Check 1',
                'Check 1 Description',
                CriticalityLevel::HIGH,
                CheckerIcon::UNKNOWN
            ),
        ];

        $this->processorMock->method('process')->willReturn($mockResults);

        // Call the method
        $result = $this->handler->getHealthCheckResult();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(HealthCheck::class, $result[0]);
    }

    public function testGetNodeSystemReturnsCorrectSystemNetwork(): void
    {
        $mockResults = [
            new HealthCheck(
                new CheckInfo('Check 1', true),
                'check_1',
                'Check 1',
                'Check 1 Description',
                CriticalityLevel::HIGH,
                CheckerIcon::UNKNOWN
            ),
        ];

        $this->processorMock->method('process')->willReturn($mockResults);

        $mockSystemNetwork = new SystemNetwork([], []);
        $this->nodeTransformerMock->method('transform')->willReturn($mockSystemNetwork);

        $result = $this->handler->getNodeSystem();

        $this->assertInstanceOf(SystemNetwork::class, $result);
        $this->assertSame($mockSystemNetwork, $result);
    }
}
