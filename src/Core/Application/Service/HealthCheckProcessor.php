<?php

declare(strict_types=1);

namespace Tax16\SystemCheckBundle\Core\Application\Service;

use Tax16\SystemCheckBundle\Core\Domain\Enum\CriticalityLevel;
use Tax16\SystemCheckBundle\Core\Domain\Model\CheckInfo;
use Tax16\SystemCheckBundle\Core\Domain\Model\HealthCheck;
use Tax16\SystemCheckBundle\Core\Domain\Port\ApplicationLoggerInterface;
use Tax16\SystemCheckBundle\Core\Domain\Service\HealthCheckProcessorInterface;
use Tax16\SystemCheckBundle\Core\Domain\Service\ServiceCheckInterface;

class HealthCheckProcessor implements HealthCheckProcessorInterface
{
    /**
     * @var array<int, array<HealthCheck>>
     */
    private $cachedResults = [
        0 => [], 1 => [],
    ];

    /**
     * @var iterable<array<string, mixed>>
     */
    private $healthChecks;

    /**
     * @var ApplicationLoggerInterface
     */
    private $logger;

    /**
     * @param iterable<array<string, mixed>> $healthChecks
     */
    public function __construct(iterable $healthChecks, ApplicationLoggerInterface $logger)
    {
        foreach ($healthChecks as $check) {
            if (!$check['service'] instanceof ServiceCheckInterface) {
                throw new \InvalidArgumentException('Service must implement HealthCheckServiceInterface');
            }
        }
        $this->healthChecks = $healthChecks;
        $this->logger = $logger;
    }

    public function process(bool $withNetwork = false): array
    {
        if ($cachedResults = $this->getCachedResults($withNetwork)) {
            return $cachedResults;
        }

        $results = [];
        foreach ($this->healthChecks as $check) {
            if (!$withNetwork && $check['parent']) {
                continue;
            }
            $this->isValidParentService($check);
            $results[] = $this->createHealthCheckDTO($check, $withNetwork);
        }
        $this->buildHierarchy($results);

        $this->cachedResults[$withNetwork] = $results;
        $this->logger->info('Health checks processed.', ['results' => $this->cachedResults]);

        return $this->cachedResults[$withNetwork];
    }

    /**
     * Clear cached results.
     */
    public function clearCache(): void
    {
        $this->cachedResults = [
            0 => [], 1 => [],
        ];
    }

    /**
     * Retrieve cached results if available.
     *
     * @return array<HealthCheck>
     */
    private function getCachedResults(bool $withNetwork = false): array
    {
        if (null !== $this->cachedResults[$withNetwork]) {
            $this->logger->info('Cached health check results loaded.', ['results' => $this->cachedResults]);
        }

        return $this->cachedResults[$withNetwork];
    }

    /**
     * @param array<string, mixed> $check
     */
    private function isValidParentService(array $check): void
    {
        if (empty($check['parent'])) {
            return;
        }

        if ($check['parent'] === $check['id']) {
            $this->logger->error('Circular reference detected in health check configuration: '.$check['id']);
            throw new \InvalidArgumentException('Circular reference detected in health check configuration: '.$check['id']);
        }

        $parentCheck = $this->findParentOnConfiguredService((array) $this->healthChecks, $check['parent']);

        if (!$parentCheck->isAllowedToHaveChildren()) {
            $this->logger->error(sprintf('Service %s does not allow children.', $check['parent']));
            throw new \InvalidArgumentException(sprintf('Service %s does not allow children.', $check['parent']));
        }
    }

    /**
     * @param mixed[] $configured
     */
    private function findParentOnConfiguredService(array $configured, string $parentId): ServiceCheckInterface
    {
        foreach ($configured as $configuredCheck) {
            if ($configuredCheck['id'] === $parentId) {
                return $configuredCheck['service'];
            }
        }

        throw new \InvalidArgumentException('Parent health check not found in configured health checks.');
    }

    /**
     * Create a HealthCheckDTO from a service check definition.
     *
     * @param array<string, mixed> $check
     */
    private function createHealthCheckDTO(array $check, bool $withNetwork = false): HealthCheck
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

        $resultData = $check['service']->check($withNetwork);

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

            if (null !== $parentId) {
                $parent = $this->findParentOnTheResults($results, $parentId);

                if (null !== $parent) {
                    $parent->getResult()->addChildren($check);
                    unset($results[$index]);
                } else {
                    $this->logger->warning('Parent health check not found for child check.', ['parent_id' => $parentId]);
                }
            }
        }

        $results = array_values($results);
    }

    /**
     * Find a parent HealthCheckDTO by ID recursively.
     *
     * @param array<HealthCheck> $results
     */
    private function findParentOnTheResults(array $results, string $parentId): ?HealthCheck
    {
        foreach ($results as $check) {
            if ($check->getId() === $parentId) {
                return $check;
            }

            if ($check->getResult()->hasChildren()) {
                $foundParent = $this->findParentOnTheResults($check->getResult()->getChildren() ?? [], $parentId);
                if (null !== $foundParent) {
                    return $foundParent;
                }
            }
        }

        return null;
    }
}
