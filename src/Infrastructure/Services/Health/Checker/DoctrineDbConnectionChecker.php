<?php

declare(strict_types=1);

namespace Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker;

use Doctrine\ORM\EntityManagerInterface;
use Tax16\SystemCheckBundle\Core\Domain\Constant\CheckerIcon;
use Tax16\SystemCheckBundle\Core\Domain\Model\CheckInfo;

class DoctrineDbConnectionChecker extends AbstractChecker
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var string
     */
    private $connectionName;

    /**
     * @param EntityManagerInterface $entityManager  the Doctrine Entity Manager for the connection
     * @param string                 $connectionName the name of the connection to check (default: 'default')
     */
    public function __construct(EntityManagerInterface $entityManager, string $connectionName = 'default')
    {
        parent::__construct('Doctrine DB', CheckerIcon::DATABASE);

        $this->entityManager = $entityManager;
        $this->connectionName = $connectionName;
    }

    /**
     * Check the Doctrine DB connection by pinging the database.
     *
     * @return CheckInfo the result of the Doctrine DB connection check
     */
    public function check(bool $withNetwork = false): CheckInfo
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
}
