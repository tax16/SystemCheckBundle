<?php

namespace Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker;

use Elastica\Client;
use Tax16\SystemCheckBundle\Core\Domain\Constant\CheckerIcon;
use Tax16\SystemCheckBundle\Core\Domain\Model\CheckInfo;

class ElasticChecker extends AbstractChecker
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        parent::__construct('Elastic Search', CheckerIcon::ELASTIC);

        $this->client = $client;
    }

    public function check(bool $withNetwork = false): CheckInfo
    {
        try {
            $health = $this->client->getCluster()->getHealth();

            if (!$health->getData() || !in_array($health->getData()['status'], ['green', 'yellow'])) {
                return  new CheckInfo(
                    $this->getName(),
                    false,
                    sprintf('Failed to connect to the elastic client, status: %s', $health->getData()['status'])
                );
            }

            return  new CheckInfo(
                $this->getName(),
                true,
                'Elastic client connected successfully'
            );

        } catch (\Exception $exception) {
            return new CheckInfo(
                $this->getName(),
                false,
                sprintf('Failed to connect to the elastic client: %s', $exception->getMessage()),
                $exception->getTraceAsString()
            );
        }
    }
}
