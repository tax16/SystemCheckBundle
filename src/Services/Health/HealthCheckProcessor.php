<?php

namespace Tax16\SystemCheckBundle\Services\Health;

use Psr\Log\LoggerInterface;
use Tax16\SystemCheckBundle\DTO\HealthCheckDTO;
use Tax16\SystemCheckBundle\Services\Health\Checker\ServiceCheckInterface;

class HealthCheckProcessor
{
    /**
     * @var array<HealthCheckDTO>|null
     */
    private ?array $cachedResults = null;

    /**
     * @var iterable<array<string, mixed>>
     */
    private iterable $healthChecks;
    private LoggerInterface $logger;

    /**
     * @param iterable<array<string, mixed>> $healthChecks
     */
    public function __construct(iterable $healthChecks, LoggerInterface $logger)
    {
        $this->healthChecks = $healthChecks;
        $this->logger = $logger;
    }

    /**
     * Perform all health checks and return their results.
     *
     * @return array<HealthCheckDTO>
     */
    public function performChecks(): array
    {
        if ($cachedResults = $this->getCachedResults()) {
            return $cachedResults;
        }

        $results = [];
        $childResults = [];

        foreach ($this->healthChecks as $check) {
            if (!$this->isValidServiceCheck($check)) {
                continue;
            }

            $dto = $this->createHealthCheckDTO($check);
            if (!isset($check['parent'])) {
                $results[$check['id']] = $dto;
            } else {
                $childResults[(string) $check['parent']][] = $dto;
            }
        }

        $this->associateChildChecks($results, $childResults);

        $this->cachedResults = array_values($results);
        $this->logger->info('Health checks processed.', ['results' => $this->cachedResults]);

        return $this->cachedResults;
    }

    /**
     * Clear cached results.
     */
    public function clearCache(): void
    {
        $this->cachedResults = null;
    }

    /**
     * Retrieve cached results if available.
     *
     * @return array<HealthCheckDTO>|null
     */
    private function getCachedResults(): ?array
    {
        if (null !== $this->cachedResults) {
            $this->logger->info('Cached health check results loaded.', ['results' => $this->cachedResults]);
        }

        return $this->cachedResults;
    }

    /**
     * Validate that the service check meets requirements.
     *
     * @param array<string, mixed> $check
     */
    private function isValidServiceCheck(array $check): bool
    {
        if (!isset($check['service']) || !$check['service'] instanceof ServiceCheckInterface) {
            $this->logger->error('Invalid health check service provided.', ['service' => $check['service'] ?? 'unknown']);

            return false;
        }

        return true;
    }

    /**
     * Create a HealthCheckDTO from a service check definition.
     *
     * @param array<string, mixed> $check
     */
    private function createHealthCheckDTO(array $check): HealthCheckDTO
    {
        $resultData = $check['service']->check();

        return new HealthCheckDTO(
            $resultData,
            $check['id'] ?? 'unknown',
            $check['label'] ?? 'unknown',
            $check['description'] ?? 'No description provided',
            $check['priority'] ?? 0,
            $check['service']->getIcon(),
            $check['parent'] ?? null
        );
    }

    /**
     * Associate child checks with their respective parent results.
     *
     * @param array<string, HealthCheckDTO>        $results
     * @param array<string, array<HealthCheckDTO>> $childResults
     */
    private function associateChildChecks(array &$results, array $childResults): void
    {
        foreach ($childResults as $parentId => $children) {
            if (isset($results[$parentId])) {
                foreach ($children as $child) {
                    $results[$parentId]->getResult()->addChildren($child);
                }
            } else {
                $this->logger->warning('Parent health check not found for child checks.', ['parent_id' => $parentId]);
            }
        }
    }
}
