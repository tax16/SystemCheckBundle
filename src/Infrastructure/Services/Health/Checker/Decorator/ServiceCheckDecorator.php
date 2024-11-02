<?php

namespace Tax16\SystemCheckBundle\Services\Health\Checker\Decorator;

use Tax16\SystemCheckBundle\Core\Domain\Model\CheckInfo;
use Tax16\SystemCheckBundle\Core\Domain\Model\Eav;
use Tax16\SystemCheckBundle\Core\Domain\Service\ServiceCheckInterface;

class ServiceCheckDecorator implements ServiceCheckInterface
{
    private const EAV_DURATION = 'duration (s)';

    private const EAV_MEMORY_USE = 'memory_usage (MB)';

    private const EAV_CPU_USE = 'cpu_load';

    private $decoratedService;

    public function __construct(ServiceCheckInterface $decoratedService)
    {
        $this->decoratedService = $decoratedService;
    }

    public function check(): CheckInfo
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        $startCpuLoad = $this->getCpuLoad();

        $result = $this->decoratedService->check();

        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        $endCpuLoad = $this->getCpuLoad();

        $duration = $endTime - $startTime;
        $memoryUsage = $endMemory - $startMemory;
        $cpuLoad = $endCpuLoad - $startCpuLoad;

        $result->addEav(new Eav(self::EAV_DURATION, number_format($duration, 3)));
        $result->addEav(new Eav(self::EAV_MEMORY_USE, number_format($memoryUsage / 1024 / 1024, 3)));
        $result->addEav(new Eav(self::EAV_CPU_USE, $cpuLoad));

        return $result;
    }

    public function getName(): string
    {
        return $this->decoratedService->getName();
    }

    public function getIcon(): ?string
    {
        return $this->decoratedService->getIcon();
    }

    private function getCpuLoad(): float
    {
        $load = sys_getloadavg();

        return is_array($load) ? $load[0] : 0.0;
    }

    public function isAllowedToHaveChildren(): bool
    {
        return $this->decoratedService->isAllowedToHaveChildren();;
    }
}
