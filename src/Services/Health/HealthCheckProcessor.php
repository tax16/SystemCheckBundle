<?php

namespace Tax16\SystemCheckBundle\Services\Health;

use Psr\Log\LoggerInterface;
use Tax16\SystemCheckBundle\DTO\CheckResult;
use Tax16\SystemCheckBundle\DTO\HealthCheckDTO;
use Tax16\SystemCheckBundle\Enum\CriticalityLevel;
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

        // Create health checks and prepare for hierarchy
        $results = [];
        foreach ($this->healthChecks as $check) {
            if (!$this->isValidServiceCheck($check)) {
                continue;
            }
            // Store in a flat array
            $results[] = $this->createHealthCheckDTO($check);
        }

        // Build parent-child relationships
        $this->buildHierarchy($results);

        // Cache and return the results
        $this->cachedResults = $results;
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
        if (false === $check['execute']) {
            return new HealthCheckDTO(
                new CheckResult(
                    $check['service']->getName(),
                    null,
                    'Not be able to check'
                ),
                $check['id'] ?? 'unknown',
                $check['label'] ?? 'unknown',
                $check['description'] ?? 'No description provided',
                $check['priority'] ?? CriticalityLevel::LOW,
                $check['service']->getIcon(),
                $check['parent'] ?? null
            );
        }

        $resultData = $check['service']->check();

        return new HealthCheckDTO(
            $resultData,
            $check['id'] ?? 'unknown',
            $check['label'] ?? 'unknown',
            $check['description'] ?? 'No description provided',
            $check['priority'] ?? CriticalityLevel::LOW,
            $check['service']->getIcon(),
            $check['parent'] ?? null
        );
    }

    /**
     * Build parent-child relationships among health checks.
     *
     * @param array<HealthCheckDTO> $results
     */
    private function buildHierarchy(array &$results): void
    {
        foreach ($results as $index => $check) {
            $parentId = $check->getParent();

            if ($parentId !== null) {
                // Try to find the parent in the main results array
                $parent = $this->findParent($results, $parentId);

                if ($parent !== null) {
                    // Add this check as a child to the found parent
                    $parent->getResult()->addChildren($check);
                    // Remove the child from the main results array
                    unset($results[$index]);
                } else {
                    // If parent does not exist, log a warning
                    $this->logger->warning('Parent health check not found for child check.', ['parent_id' => $parentId]);
                }
            }
        }

        // Re-index the results array after unsetting elements
        $results = array_values($results);
    }

    /**
     * Find a parent HealthCheckDTO by ID recursively.
     *
     * @param array<HealthCheckDTO> $results
     * @param string $parentId
     * @return HealthCheckDTO|null
     */
    private function findParent(array $results, string $parentId): ?HealthCheckDTO
    {
        foreach ($results as $check) {
            if ($check->getId() === $parentId) {
                return $check;
            }

            if ($check->getResult()->hasChildren()) {
                $foundParent = $this->findParent($check->getResult()->getChildren(), $parentId);
                if ($foundParent !== null) {
                    return $foundParent;
                }
            }
        }

        // Return null if parent not found
        return null;
    }
}
