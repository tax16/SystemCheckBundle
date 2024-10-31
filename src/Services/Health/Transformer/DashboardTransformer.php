<?php

namespace Tax16\SystemCheckBundle\Services\Health\Transformer;

use Tax16\SystemCheckBundle\DTO\HealthCheckCategoryDTO;
use Tax16\SystemCheckBundle\DTO\HealthCheckDTO;
use Tax16\SystemCheckBundle\Enum\CriticalityLevel;

class DashboardTransformer implements TransformerInterface
{
    /**
     * @param array<HealthCheckDTO> $results
     */
    public function transform(array $results): HealthCheckCategoryDTO
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

        return new HealthCheckCategoryDTO($successChecks, $failedChecks, $warningChecks);
    }
}
