<?php

namespace Tax16\SystemCheckBundle\Services\Health;

use Psr\Log\LoggerInterface;
use Tax16\SystemCheckBundle\DTO\HealthCheckDTO;

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

    public function performChecks(): array
    {
        $results = [];
        foreach ($this->healthChecks as $check) {
            $result = $check['service']->check();

            $results[] = new HealthCheckDTO(
                $result,
                $check['label'],
                $check['description'],
                $check['priority']
            );
        }

        dump($results);die;
        return $results;
    }
}