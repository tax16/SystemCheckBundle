<?php

namespace unit\Services\Health;

use Tax16\SystemCheckBundle\DTO\CheckResult;
use Tax16\SystemCheckBundle\DTO\HealthCheckCategoryDTO;
use Tax16\SystemCheckBundle\DTO\HealthCheckDTO;
use Tax16\SystemCheckBundle\Enum\CriticalityLevel;
use Tax16\SystemCheckBundle\Services\Health\Checker\Constant\CheckerIcon;
use Tax16\SystemCheckBundle\Services\Health\HealthCheckHandler;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Tax16\SystemCheckBundle\Services\Health\HealthCheckProcessor;
use Tax16\SystemCheckBundle\Services\Health\Transformer\DashboardTransformer;
use Tax16\SystemCheckBundle\Services\Health\Transformer\NodeTransformer;
use Tax16\SystemCheckBundle\ValueObject\SystemNetwork;

class HealthCheckHandlerTest extends TestCase
{
    private HealthCheckHandler $handler;
    private LoggerInterface $loggerMock;
    private HealthCheckProcessor $processorMock;
    private DashboardTransformer $dashboardTransformerMock;
    private NodeTransformer $nodeTransformerMock;

    protected function setUp(): void
    {
        // Create a mock logger
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        // Create mock for HealthCheckProcessor
        $this->processorMock = $this->createMock(HealthCheckProcessor::class);

        // Create mock for DashboardTransformer
        $this->dashboardTransformerMock = $this->createMock(DashboardTransformer::class);

        // Create mock for NodeTransformer
        $this->nodeTransformerMock = $this->createMock(NodeTransformer::class);

        // Instantiate the HealthCheckHandler with mocked dependencies
        $this->handler = new HealthCheckHandler(
            $this->loggerMock,
            $this->processorMock,
            $this->dashboardTransformerMock,
            $this->nodeTransformerMock
        );
    }

    public function testGetHealthCheckDashboardReturnsCorrectDTO(): void
    {
        // Mocking the results from the HealthCheckProcessor
        $mockResults = [
            new HealthCheckDTO(
                new CheckResult('Check 1', true),
                'Check 1',
                'Check 1 Description',
                CriticalityLevel::HIGH,
                CheckerIcon::UNKNOWN
            ),
            new HealthCheckDTO(
                new CheckResult('Check 2', false),
                'Check 2',
                'Check 2 Description',
                CriticalityLevel::LOW,
                CheckerIcon::UNKNOWN
            ),
        ];

        $this->processorMock->method('performChecks')->willReturn($mockResults);

        // Mocking the dashboard transformation
        $mockDashboardDTO = new HealthCheckCategoryDTO([], [], []);
        $this->dashboardTransformerMock->method('transform')->willReturn($mockDashboardDTO);

        // Call the method
        $result = $this->handler->getHealthCheckDashboard();

        // Assert that the result is of type HealthCheckCategoryDTO
        $this->assertInstanceOf(HealthCheckCategoryDTO::class, $result);
        $this->assertSame($mockDashboardDTO, $result); // Assert that the returned DTO is the same as the mocked one
    }

    public function testGetHealthCheckResultReturnsCorrectArray(): void
    {
        // Mocking the results from the HealthCheckProcessor
        $mockResults = [
            new HealthCheckDTO(
                new CheckResult('Check 1', true),
                'Check 1',
                'Check 1 Description',
                CriticalityLevel::HIGH,
                CheckerIcon::UNKNOWN
            ),
        ];

        $this->processorMock->method('performChecks')->willReturn($mockResults);

        // Call the method
        $result = $this->handler->getHealthCheckResult();

        // Assert that the result is an array
        $this->assertIsArray($result);
        $this->assertCount(1, $result); // Expecting one health check result
        $this->assertInstanceOf(HealthCheckDTO::class, $result[0]); // Assert type of the first element
    }

    public function testGetNodeSystemReturnsCorrectSystemNetwork(): void
    {
        // Mocking the results from the HealthCheckProcessor
        $mockResults = [
            new HealthCheckDTO(
                new CheckResult('Check 1', true),
                'Check 1',
                'Check 1 Description',
                CriticalityLevel::HIGH,
                CheckerIcon::UNKNOWN
            ),
        ];

        $this->processorMock->method('performChecks')->willReturn($mockResults);

        // Mock the SystemNetwork return value for node transformation
        $mockSystemNetwork = new SystemNetwork([], []);
        $this->nodeTransformerMock->method('transform')->willReturn($mockSystemNetwork);

        // Call the method
        $result = $this->handler->getNodeSystem();

        // Assert that the result is of type SystemNetwork
        $this->assertInstanceOf(SystemNetwork::class, $result);
        $this->assertSame($mockSystemNetwork, $result); // Assert that the returned system network is the same as the mocked one
    }
}
