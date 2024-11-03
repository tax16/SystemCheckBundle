<?php

declare(strict_types=1);

namespace Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker\Rabbit;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Tax16\SystemCheckBundle\Core\Domain\Enum\RabbitMQMode;

class RabbitMQFactory
{
    /**
     * @param AMQPStreamConnection $connection
     * @param string $queue
     * @param string $mode
     * @return RabbitMQConsumer|RabbitMQSender|void
     */
    public static function create(AMQPStreamConnection $connection, string $queue, string $mode)
    {
        assert(RabbitMQMode::isValid($mode));

        switch ($mode) {
            case RabbitMQMode::SENDER:
                return new RabbitMQSender($connection, $queue);
            case RabbitMQMode::CONSUMER:
                return new RabbitMQConsumer($connection, $queue);
            default:
                throw new \InvalidArgumentException('Invalid mode');
        }
    }
}
