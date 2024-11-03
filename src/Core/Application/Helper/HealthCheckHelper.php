<?php

declare(strict_types=1);

namespace Tax16\SystemCheckBundle\Core\Application\Helper;

use Tax16\SystemCheckBundle\Core\Domain\Model\HealthCheck;

class HealthCheckHelper
{
    /**
     * @param array<HealthCheck> $healthChecks
     * @return int
     */
    public static function countHealthChecks(array $healthChecks): int
    {
        $count = 0;
        foreach ($healthChecks as $healthCheck)
        {
            $count++;
            if (count($healthCheck->getResult()->getChildren() ?? []) > 0) {
                $count += self::countHealthChecks($healthCheck->getResult()->getChildren());
            }
        }

        return $count;
    }

    /**
     * @param array<HealthCheck> $healthChecks
     * @return HealthCheck[]
     */
    public static function listAllHealthChecks(array $healthChecks): array
    {
        $results = [];

        foreach ($healthChecks as $healthCheck) {
            $results[] = $healthCheck;
            $children = $healthCheck->getResult()->getChildren();
            if (!empty($children)) {
                $childResults = self::listAllHealthChecks($children);
                foreach ($childResults as $child) {
                    $results[] = $child;
                }
                $healthCheck->getResult()->setChildren([]);
            }
        }

        return $results;
    }
}