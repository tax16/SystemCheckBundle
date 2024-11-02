<?php

namespace Tax16\SystemCheckBundle\Core\Application\Service;

use InvalidArgumentException;
use Tax16\SystemCheckBundle\Core\Domain\Enum\CriticalityLevel;
use Tax16\SystemCheckBundle\Core\Domain\Model\CheckInfo;
use Tax16\SystemCheckBundle\Core\Domain\Model\HealthCheck;
use Tax16\SystemCheckBundle\Core\Domain\Port\ApplicationLoggerInterface;
use Tax16\SystemCheckBundle\Core\Domain\Service\HealthCheckProcessorInterface;
use Tax16\SystemCheckBundle\Core\Domain\Service\ServiceCheckInterface;

class HealthCheckProcessor implements HealthCheckProcessorInterface
{
    /**
     * @var array<HealthCheck>|null
     */
    private $cachedResults;

    /**
     * @var iterable<array<string, mixed>>
     */
    private $healthChecks;
    private $logger;

    /**
     * @param iterable<array<string, mixed>> $healthChecks
     */
    public function __construct(iterable $healthChecks, ApplicationLoggerInterface $logger)
    {
        foreach ($healthChecks as $check) {
            if (!$check['service'] instanceof ServiceCheckInterface) {
                throw new InvalidArgumentException('Service must implement HealthCheckServiceInterface');
            }
        }
        $this->healthChecks = $healthChecks;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function process(): array
    {
        if ($cachedResults = $this->getCachedResults()) {
            return $cachedResults;
        }

        $results = [];
        foreach ($this->healthChecks as $check) {
            $this->isValidParentService($check);
            $results[] = $this->createHealthCheckDTO($check);
        }

        $this->buildHierarchy($results);

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
     * @return array<HealthCheck>|null
     */
    private function getCachedResults(): ?array
    {
        if (null !== $this->cachedResults) {
            $this->logger->info('Cached health check results loaded.', ['results' => $this->cachedResults]);
        }

        return $this->cachedResults;
    }

    /**
     * @param array $check
     * @param array<string, mixed> $check
     */
    private function isValidParentService(array $check): bool
    {
        if (empty($check['parent'])) {
            return true;
        }

        if ($check['parent'] === $check['id']) {
            $this->logger->error('Circular reference detected in health check configuration: ' . $check['id']);
            throw new InvalidArgumentException('Circular reference detected in health check configuration: ' . $check['id']);
        }

        $parentCheck = $this->findParentOnConfiguredService($this->healthChecks, $check['parent']);

        if (!$parentCheck->isAllowedToHaveChildren()) {
            $this->logger->error(sprintf('Service %s does not allow children.', $check['parent']));
            throw new InvalidArgumentException(sprintf('Service %s does not allow children.', $check['parent']));
        }

        return true;
    }

    private function findParentOnConfiguredService(array $configured, string $parentId): ServiceCheckInterface
    {
        foreach ($configured as $configuredCheck) {
            if ($configuredCheck['id'] === $parentId) {
                return $configuredCheck['service'];
            }
        }

        throw new InvalidArgumentException('Parent health check not found in configured health checks.');
    }

    /**
     * Create a HealthCheckDTO from a service check definition.
     *
     * @param array<string, mixed> $check
     */
    private function createHealthCheckDTO(array $check): HealthCheck
    {
        if (false === $check['execute']) {
            return new HealthCheck(
                new CheckInfo(
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

        return new HealthCheck(
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
     * @param array<HealthCheck> $results
     */
    private function buildHierarchy(array &$results): void
    {
        foreach ($results as $index => $check) {
            $parentId = $check->getParent();

            if ($parentId !== null) {
                // Try to find the parent in the main results array
                $parent = $this->findParentOnTheResults($results, $parentId);

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
     * @param array<HealthCheck> $results
     * @param string $parentId
     * @return HealthCheck|null
     */
    private function findParentOnTheResults(array $results, string $parentId): ?HealthCheck
    {
        foreach ($results as $check) {
            if ($check->getId() === $parentId) {
                return $check;
            }

            if ($check->getResult()->hasChildren()) {
                $foundParent = $this->findParentOnTheResults($check->getResult()->getChildren(), $parentId);
                if ($foundParent !== null) {
                    return $foundParent;
                }
            }
        }

        // Return null if parent not found
        return null;
    }
}
