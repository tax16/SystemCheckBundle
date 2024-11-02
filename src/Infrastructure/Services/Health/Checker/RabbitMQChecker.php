<?php

namespace Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Tax16\SystemCheckBundle\Core\Domain\Constant\CheckerIcon;
use Tax16\SystemCheckBundle\Core\Domain\Enum\RabbitMQMode;
use Tax16\SystemCheckBundle\Core\Domain\Model\CheckInfo;
use Tax16\SystemCheckBundle\Core\Domain\Model\HealthCheck;
use Tax16\SystemCheckBundle\Core\Domain\Service\ServiceCheckInterface;
use Tax16\SystemCheckBundle\Services\Health\Checker\Rabbit\RabbitMQConsumer;
use Tax16\SystemCheckBundle\Services\Health\Checker\Rabbit\RabbitMQFactory;
use Tax16\SystemCheckBundle\Services\Health\Checker\Rabbit\RabbitMQSender;

class RabbitMQChecker implements ServiceCheckInterface
{
    private $connection;
    private $queue;
    private $mode;
    private $host;
    private $port;
    private $username;
    private $password;
    private $vhost;
    private $cacert;
    /**
     * @var HealthCheck[]
     */
    private $childrenChecker = [];

    public function __construct(
        string $host,
        int $port,
        string $username,
        string $password,
        string $queue,
        string $mode,
        string $vhost = '/',
        ?string $cacert = null
    ) {
        assert(RabbitMQMode::isValid($mode));

        $this->queue = $queue;
        $this->mode = $mode;
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->vhost = $vhost;
        $this->cacert = $cacert;
    }

    /**
     * @return RabbitMQConsumer|RabbitMQSender|null
     * @throws \Exception
     */
    private function createClientConnection()
    {
        $this->connection = new AMQPStreamConnection(
            $this->host,
            $this->port,
            $this->username,
            $this->password,
            $this->vhost
        );

        return RabbitMQFactory::create($this->connection, $this->queue, $this->mode);
    }

    private function checkClientConnection(): void
    {
        if (RabbitMQMode::PING === $this->mode) {
            $connection = new \AMQPConnection([
                'host' => $this->host,
                'port' => $this->port,
                'login' => $this->username,
                'password' => $this->password,
                'vhost' => $this->vhost,
                'cacert' => $this->cacert,
            ]);

            $connection->connect();
            return;
        }

        $client = $this->createClientConnection();

        if ($client instanceof RabbitMQSender) {
            $client->sendMessage('Health Check');
            return;
        }

        $client->consumeMessage();
    }

    public function check(): CheckInfo
    {
        try {
            $this->checkClientConnection();

            return new CheckInfo(
                'RabbitMQ Health Check',
                true,
                'RabbitMQ check successful.',
                null
            );
        } catch (\Exception $e) {
            return new CheckInfo(
                'RabbitMQ Health Check',
                false,
                sprintf('Failed to connect to RabbitMQ: %s', $e->getMessage()),
                $e->getTraceAsString()
            );
        }
    }

    public function getName(): string
    {
        return 'RabbitMQ Health';
    }

    public function getIcon(): ?string
    {
        return CheckerIcon::RABBIT_MQ;
    }

    public function isAllowedToHaveChildren(): bool
    {
        return true;
    }
}
