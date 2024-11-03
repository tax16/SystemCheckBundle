<?php

declare(strict_types=1);

namespace Tax16\SystemCheckBundle\Core\Domain\Service;

use Tax16\SystemCheckBundle\Core\Domain\Model\HealthCheck;

interface HealthCheckProcessorInterface
{
    /**
     * Perform all health checks and return their results.
     *
     * @return array<HealthCheck>
     */
    public function process(bool $withNetwork = false): array;
}