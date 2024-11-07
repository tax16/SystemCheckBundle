<?php

namespace Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Tax16\SystemCheckBundle\Core\Application\Helper\StringHelper;
use Tax16\SystemCheckBundle\Core\Domain\Constant\CheckerIcon;
use Tax16\SystemCheckBundle\Core\Domain\Enum\RabbitMQMode;
use Tax16\SystemCheckBundle\Core\Domain\Model\CheckInfo;
use Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker\Rabbit\RabbitMQConsumer;
use Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker\Rabbit\RabbitMQFactory;
use Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker\Rabbit\RabbitMQSender;

class RabbitMQChecker extends AbstractChecker
{
    /**
     * @var AMQPStreamConnection
     */
    private $connection;

    /**
     * @var string
     */
    private $queue;

    /**
     * @var string
     */
    private $mode;

    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $vhost;

    /**
     * @var string|null
     */
    private $cacert;

    public function __construct(
        string $url,
        string $mode = RabbitMQMode::PING
    ) {
        parent::__construct('RabbitMQ Health', CheckerIcon::RABBIT_MQ, true);

        assert(RabbitMQMode::isValid($mode));
        $this->parseConnectionUrl($url);
    }

    /**
     * @return RabbitMQConsumer|RabbitMQSender
     *
     * @throws \Exception
     */
    private function createClientConnection()
    {
        $this->connection = new AMQPStreamConnection(
            $this->host,
            (string) $this->port,
            $this->username,
            $this->password,
            $this->vhost
        );

        return RabbitMQFactory::create($this->connection, $this->queue, $this->mode);
    }

    private function parseConnectionUrl(string $url): void
    {
        $parsedUrl = parse_url($url);
        if (false === $parsedUrl || !isset($parsedUrl['host'], $parsedUrl['port'], $parsedUrl['user'], $parsedUrl['pass'], $parsedUrl['path'])) {
            throw new \InvalidArgumentException('Invalid URL format for RabbitMQ connection.');
        }

        $this->host = $parsedUrl['host'];
        $this->port = (int) $parsedUrl['port'];
        $this->username = $parsedUrl['user'];
        $this->password = $parsedUrl['pass'];
        $this->vhost = ltrim($parsedUrl['path'], '/');
        /**
         * @var string[] $queryParams
         */
        $queryParams = [];
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $queryParams);
        }

        $this->queue = StringHelper::castToString($queryParams['queue'] ?? null) ?? 'defaultQueue';
        $this->mode = StringHelper::castToString($queryParams['mode'] ?? null) ?? RabbitMQMode::PING;
        $this->cacert = StringHelper::castToString($queryParams['cacert'] ?? null);
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

    public function check(bool $withNetwork = false): CheckInfo
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
}
