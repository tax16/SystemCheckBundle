<?php

namespace Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker;

use Doctrine\ORM\EntityManagerInterface;
use Tax16\SystemCheckBundle\Core\Domain\Constant\CheckerIcon;
use Tax16\SystemCheckBundle\Core\Domain\Model\CheckInfo;
use Tax16\SystemCheckBundle\Core\Domain\Model\HealthCheck;
use Tax16\SystemCheckBundle\Core\Domain\Service\ServiceCheckInterface;

class DoctrineDbConnectionChecker implements ServiceCheckInterface
{
    private $entityManager;
    private $connectionName;

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
     * @return CheckInfo the result of the Doctrine DB connection check
     */
    public function check(): CheckInfo
    {
        try {
            $connection = $this->entityManager->getConnection();
            if (!$connection->isConnected()) {
                $connection->executeQuery('SELECT 1');
            }

            return new CheckInfo(
                $this->getName(),
                true,
                sprintf('Connection to the database "%s" is successful.', $this->connectionName),
                null
            );
        } catch (\Exception $e) {
            return new CheckInfo(
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
        return 'Doctrine DB';
    }

    public function getIcon(): ?string
    {
        return CheckerIcon::DATABASE;
    }

    /**
     * @param array<HealthCheck> $childrenChecker
     */
    public function setChildren(array $childrenChecker): void
    {
        throw new \InvalidArgumentException('Not accept child process');
    }

    public function isAllowedToHaveChildren(): bool
    {
        return false;
    }
}
