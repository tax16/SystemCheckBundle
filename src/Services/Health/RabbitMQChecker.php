<?php

namespace Tax16\SystemCheckBundle\Services\Health;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Tax16\SystemCheckBundle\Services\Health\Class\RabbitMQConsumer;
use Tax16\SystemCheckBundle\Services\Health\Class\RabbitMQFactory;
use Tax16\SystemCheckBundle\Services\Health\Class\RabbitMQSender;
use Tax16\SystemCheckBundle\Services\Health\DTO\CheckResult;
use Tax16\SystemCheckBundle\Services\Health\Enum\RabbitMQMode;

class RabbitMQChecker implements ServiceCheckInterface
{
    private AMQPStreamConnection $connection;
    private string $queue;
    private RabbitMQMode $mode;
    private string $host;
    private int $port;
    private string $username;
    private string $password;
    private string $vhost;
    private ?string $cacert;

    public function __construct(
        string $host,
        int $port,
        string $username,
        string $password,
        string $queue,
        RabbitMQMode $mode,
        string $vhost = '/',
        ?string $cacert = null,
    ) {
        $this->queue = $queue;
        $this->mode = $mode;
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->vhost = $vhost;
        $this->cacert = $cacert;
    }

    private function createClientConnection(): RabbitMQSender|RabbitMQConsumer
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
        match (true) {
            $client instanceof RabbitMQSender => $client->sendMessage('Health Check'),
            $client instanceof RabbitMQConsumer => $client->consumeMessage(),
        };
    }

    public function check(): CheckResult
    {
        try {
            $this->checkClientConnection();

            return new CheckResult(
                'RabbitMQ Health Check',
                true,
                'RabbitMQ check successful.',
                null
            );
        } catch (\Exception $e) {
            return new CheckResult(
                'RabbitMQ Health Check',
                false,
                sprintf('Failed to connect to RabbitMQ: %s', $e->getMessage()),
                $e->getTraceAsString()
            );
        }
    }

    public function getName(): string
    {
        return 'RabbitMQ Health Check';
    }
}
