<?php

namespace Tax16\SystemCheckBundle\Core\Application\DTO;

use Tax16\SystemCheckBundle\Core\Domain\Model\HealthCheck;

class HealthCheckCategory
{
    /**
     * @var array<HealthCheck>
     */
    private $successChecks;

    /**
     * @var array<HealthCheck>
     */
    private $failedChecks;

    /**
     * @var array<HealthCheck>
     */
    private $warningChecks;

    /**
     * @param array<HealthCheck> $successChecks
     * @param array<HealthCheck> $failedChecks
     * @param array<HealthCheck> $warningChecks
     */
    public function __construct(array $successChecks, array $failedChecks, array $warningChecks)
    {
        $this->successChecks = $successChecks;
        $this->failedChecks = $failedChecks;
        $this->warningChecks = $warningChecks;
    }

    /**
     * @return array<HealthCheck>
     */
    public function getSuccessChecks(): array
    {
        return $this->successChecks;
    }

    /**
     * @return array<HealthCheck>
     */
    public function getFailedChecks(): array
    {
        return $this->failedChecks;
    }

    /**
     * @return array<HealthCheck>
     */
    public function getWarningChecks(): array
    {
        return $this->warningChecks;
    }

    public function getServiceCount(): int
    {
        return count($this->successChecks) + count($this->failedChecks) + count($this->warningChecks);
    }
}
