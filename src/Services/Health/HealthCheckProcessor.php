<?php

namespace Tax16\SystemCheckBundle\Services\Health;

use Psr\Log\LoggerInterface;
use Tax16\SystemCheckBundle\DTO\HealthCheckDTO;

class HealthCheckProcessor
{
    /**
     * @var array<HealthCheckDTO>|null
     */
    private ?array $cachedResults = null;

    /**
     * @var array<string, mixed>
     */
    private iterable $healthChecks;
    private LoggerInterface $logger;

    /**
     * @param array<string, mixed> $healthChecks
     */
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
        if (null !== $this->cachedResults) {
            $this->logger->info('cache_result_load', $this->cachedResults);

            return $this->cachedResults;
        }

        $results = [];
        foreach ($this->healthChecks as $check) {
            $result = $check['service']->check();

            $results[] = new HealthCheckDTO(
                $result,
                $check['label'],
                $check['description'],
                $check['priority'],
                $check['service']->getIcon()
            );
        }
        $this->logger->info('services_load_result', $results);

        return $results;
    }

    public function clearCache(): void
    {
        $this->cachedResults = null;
    }
}
