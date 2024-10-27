<?php

namespace Tax16\SystemCheckBundle\Services\Health;

use Psr\Log\LoggerInterface;
use Tax16\SystemCheckBundle\DTO\CheckResult;
use Tax16\SystemCheckBundle\DTO\HealthCheckCategoryDTO;
use Tax16\SystemCheckBundle\DTO\HealthCheckDTO;
use Tax16\SystemCheckBundle\Enum\CriticalityLevel;
use Tax16\SystemCheckBundle\Services\Health\Checker\Constant\CheckerIcon;
use Tax16\SystemCheckBundle\Services\Health\Transformer\DashboardTransformer;
use Tax16\SystemCheckBundle\Services\Health\Transformer\NodeTransformer;
use Tax16\SystemCheckBundle\ValueObject\SystemNetwork;

class HealthCheckHandler
{
    private HealthCheckDTO $head;

    private LoggerInterface $logger;

    private HealthCheckProcessor $checkProcessor;

    private NodeTransformer $nodeTransformer;

    private DashboardTransformer $dashboardTransformer;

    public function __construct(
        LoggerInterface $logger,
        HealthCheckProcessor $checkProcessor,
        DashboardTransformer $dashboardTransformer,
        NodeTransformer $nodeTransformer,
    ) {
        $this->logger = $logger;
        $this->checkProcessor = $checkProcessor;
        $this->dashboardTransformer = $dashboardTransformer;
        $this->nodeTransformer = $nodeTransformer;
        $this->head = new HealthCheckDTO(
            new CheckResult(
                'Head pattern',
                true
            ),
            'Head',
            'Head check',
            'my label head',
            CriticalityLevel::HEAD,
            CheckerIcon::UNKNOWN
        );
    }

    public function getHealthCheckDashboard(): HealthCheckCategoryDTO
    {
        $result = $this->checkProcessor->performChecks();
        $this->logger->info('dashboard_view_check', $result);

        return $this->dashboardTransformer->transform(
            $result
        );
    }

    /**
     * @return array<HealthCheckDTO>
     */
    public function getHealthCheckResult(): array
    {
        return $this->checkProcessor->performChecks();
    }

    public function getNodeSystem(): SystemNetwork
    {
        $result = $this->checkProcessor->performChecks();
        $result[] = $this->head;

        $this->logger->info('network_view_check', $result);

        return $this->nodeTransformer->transform($result);
    }
}
