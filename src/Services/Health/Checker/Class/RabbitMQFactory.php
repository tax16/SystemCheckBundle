<?php

namespace Tax16\SystemCheckBundle\Services\Health\Checker\Class;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Tax16\SystemCheckBundle\Enum\RabbitMQMode;

class RabbitMQFactory
{
    public static function create(AMQPStreamConnection $connection, string $queue, RabbitMQMode $mode): RabbitMQSender|RabbitMQConsumer
    {
        return match ($mode) {
            RabbitMQMode::SENDER => new RabbitMQSender($connection, $queue),
            RabbitMQMode::CONSUMER => new RabbitMQConsumer($connection, $queue),
            default => throw new \InvalidArgumentException('Invalid mode'),
        };
    }
}
