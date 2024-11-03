<?php

namespace Tax16\SystemCheckBundle\Core\Application\Service;

use Tax16\SystemCheckBundle\Core\Application\DTO\HealthCheckCategory;
use Tax16\SystemCheckBundle\Core\Application\Transformer\DashboardTransformer;
use Tax16\SystemCheckBundle\Core\Application\Transformer\NodeTransformer;
use Tax16\SystemCheckBundle\Core\Domain\Constant\CheckerIcon;
use Tax16\SystemCheckBundle\Core\Domain\Enum\CriticalityLevel;
use Tax16\SystemCheckBundle\Core\Domain\Model\CheckInfo;
use Tax16\SystemCheckBundle\Core\Domain\Model\HealthCheck;
use Tax16\SystemCheckBundle\Core\Domain\Port\ApplicationLoggerInterface;
use Tax16\SystemCheckBundle\Core\Domain\Port\ConfigurationProviderInterface;
use Tax16\SystemCheckBundle\Core\Domain\Service\HealthCheckProcessorInterface;
use Tax16\SystemCheckBundle\Core\Domain\ValueObject\SystemNetwork;

class HealthCheckHandler
{
    /**
     * @var HealthCheck
     */
    private $head;

    /**
     * @var ApplicationLoggerInterface
     */
    private $logger;

    /**
     * @var HealthCheckProcessorInterface
     */
    private $checkProcessor;

    /**
     * @var NodeTransformer
     */
    private $nodeTransformer;

    /**
     * @var DashboardTransformer
     */
    private $dashboardTransformer;

    public function __construct(
        ApplicationLoggerInterface $logger,
        HealthCheckProcessorInterface $checkProcessor,
        DashboardTransformer $dashboardTransformer,
        NodeTransformer $nodeTransformer,
        ConfigurationProviderInterface $parameterBag
    ) {
        $this->logger = $logger;
        $this->checkProcessor = $checkProcessor;
        $this->dashboardTransformer = $dashboardTransformer;
        $this->nodeTransformer = $nodeTransformer;

        $appName = $parameterBag->get('system_check.name');
        $appId = $parameterBag->get('system_check.id');

        $this->head = new HealthCheck(
            new CheckInfo(
                $appName,
                true
            ),
            $appId,
            $appName,
            'No content',
            CriticalityLevel::HEAD,
            CheckerIcon::UNKNOWN
        );
    }


    public function getHealthCheckDashboard(): HealthCheckCategory
    {
        $result = $this->checkProcessor->process();
        $this->logger->info('dashboard_view_check', $result);

        return $this->dashboardTransformer->transform($result);
    }

    /**
     * @return array<HealthCheck>
     */
    public function getHealthCheckResult(): array
    {
        return $this->checkProcessor->process(true);
    }

    public function getNodeSystem(bool $withNetwork = false): SystemNetwork
    {
        $result = $this->checkProcessor->process($withNetwork);
        $result[] = $this->head;

        $this->logger->info('network_view_check', $result);

        return $this->nodeTransformer->transform($result);
    }
}
