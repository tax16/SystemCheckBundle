<?php

namespace Tax16\SystemCheckBundle\Services\Health\Checker;

use Doctrine\ORM\EntityManagerInterface;
use Tax16\SystemCheckBundle\DTO\CheckResult;

class DoctrineDbConnectionChecker implements ServiceCheckInterface
{
    private EntityManagerInterface $entityManager;
    private string $connectionName;

    /**
     * @param EntityManagerInterface $entityManager  the Doctrine Entity Manager for the connection
     * @param string                 $connectionName the name of the connection to check (default: 'default')
     */
    public function __construct(EntityManagerInterface $entityManager, string $connectionName = 'default')
    {
        $this->entityManager = $entityManager;
        $this->connectionName = $connectionName;
    }

    /**
     * Check the Doctrine DB connection by pinging the database.
     *
     * @return CheckResult the result of the Doctrine DB connection check
     */
    public function check(): CheckResult
    {
        try {
            $connection = $this->entityManager->getConnection();
            if (!$connection->isConnected()) {
                $connection->executeQuery('SELECT 1');
            }

            return new CheckResult(
                $this->getName(),
                true,
                sprintf('Connection to the database "%s" is successful.', $this->connectionName),
                null
            );
        } catch (\Exception $e) {
            return new CheckResult(
                $this->getName(),
                false,
                sprintf('Failed to connect to the database "%s": %s', $this->connectionName, $e->getMessage()),
                $e->getTraceAsString()
            );
        }
    }

    /**
     * Get the name of the health check.
     */
    public function getName(): string
    {
        return 'Doctrine DB Connection Check';
    }
}
