<?php

namespace Tax16\SystemCheckBundle\Services\Health;

use Psr\Log\LoggerInterface;
use Tax16\SystemCheckBundle\DTO\HealthCheckCategoryDTO;
use Tax16\SystemCheckBundle\DTO\HealthCheckDTO;
use Tax16\SystemCheckBundle\Enum\CriticalityLevel;
use Tax16\SystemCheckBundle\Services\Health\Checker\ServiceCheckInterface;

class HealthCheckManager
{
    /**
     * @var iterable
     */
    private iterable $healthChecks;
    private LoggerInterface $logger;

    public function __construct(iterable $healthChecks, LoggerInterface $logger)
    {
        $this->healthChecks = $healthChecks;
        $this->logger = $logger;
    }

    /**
     * @return array<HealthCheckDTO>
     */
    public function performChecks(): array
    {
        $results = [];
        foreach ($this->healthChecks as $check) {
            /**
             * @var ServiceCheckInterface $check['service']
             */
            $result = $check['service']->check();

            $results[] = new HealthCheckDTO(
                $result,
                $check['label'],
                $check['description'],
                $check['priority'],
                $check['service']->getIcon()
            );
        }

        return $results;
    }

    public function dashboardCheck(): HealthCheckCategoryDTO
    {
        $results = $this->performChecks();

        $successChecks = [];
        $failedChecks = [];
        $warningChecks = [];

        foreach ($results as $checkDTO) {
            $isSuccessful = $checkDTO->getResult()->isSuccess();
            $priority = $checkDTO->getPriority();

            if ($isSuccessful) {
                $successChecks[] = $checkDTO;
            } elseif (CriticalityLevel::from($priority) === CriticalityLevel::LOW) {
                $warningChecks[] = $checkDTO;
            } else {
                $failedChecks[] = $checkDTO;
            }

            if (!$isSuccessful) {
                $this->logger->error(sprintf(
                    'Health check failed for %s with message: %s',
                    $checkDTO->getLabel(),
                    $checkDTO->getResult()->getMessage()
                ));
            }
        }

        return new HealthCheckCategoryDTO($successChecks, $failedChecks, $warningChecks);
    }
}