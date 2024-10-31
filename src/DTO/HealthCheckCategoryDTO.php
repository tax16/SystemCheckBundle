<?php

namespace Tax16\SystemCheckBundle\DTO;

class HealthCheckCategoryDTO
{
    /**
     * @var array<HealthCheckDTO>
     */
    private array $successChecks;

    /**
     * @var array<HealthCheckDTO>
     */
    private array $failedChecks;

    /**
     * @var array<HealthCheckDTO>
     */
    private array $warningChecks;

    /**
     * @param array<HealthCheckDTO> $successChecks
     * @param array<HealthCheckDTO> $failedChecks
     * @param array<HealthCheckDTO> $warningChecks
     */
    public function __construct(array $successChecks, array $failedChecks, array $warningChecks)
    {
        $this->successChecks = $successChecks;
        $this->failedChecks = $failedChecks;
        $this->warningChecks = $warningChecks;
    }

    /**
     * @return array<HealthCheckDTO>
     */
    public function getSuccessChecks(): array
    {
        return $this->successChecks;
    }

    /**
     * @return array<HealthCheckDTO>
     */
    public function getFailedChecks(): array
    {
        return $this->failedChecks;
    }

    /**
     * @return array<HealthCheckDTO>
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
