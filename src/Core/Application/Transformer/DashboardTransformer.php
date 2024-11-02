<?php

namespace Tax16\SystemCheckBundle\Core\Application\Transformer;

use Tax16\SystemCheckBundle\Core\Application\DTO\HealthCheckCategory;
use Tax16\SystemCheckBundle\Core\Domain\Enum\CriticalityLevel;
use Tax16\SystemCheckBundle\Core\Domain\Model\HealthCheck;

class DashboardTransformer implements TransformerInterface
{
    /**
     * @param array<HealthCheck> $results
     */
    public function transform(array $results): HealthCheckCategory
    {
        $successChecks = [];
        $failedChecks = [];
        $warningChecks = [];

        foreach ($results as $checkDTO) {
            $isSuccessful = $checkDTO->getResult()->isSuccess();
            $priority = $checkDTO->getPriority();

            if ($isSuccessful) {
                $successChecks[] = $checkDTO;
            } elseif (CriticalityLevel::LOW === $priority) {
                $warningChecks[] = $checkDTO;
            } else {
                $failedChecks[] = $checkDTO;
            }
        }

        return new HealthCheckCategory($successChecks, $failedChecks, $warningChecks);
    }
}
