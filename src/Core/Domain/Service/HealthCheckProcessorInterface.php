<?php

namespace Tax16\SystemCheckBundle\Core\Domain\Service;

use Tax16\SystemCheckBundle\Core\Domain\Model\HealthCheck;

interface HealthCheckProcessorInterface
{
    /**
     * Perform all health checks and return their results.
     *
     * @return array<HealthCheck>
     */
    public function process(): array;
}