<?php

namespace unit\Infrastructure\Services\Health\Rabbit;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPUnit\Framework\TestCase;
use Tax16\SystemCheckBundle\Core\Domain\Enum\RabbitMQMode;
use Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker\Rabbit\RabbitMQConsumer;
use Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker\Rabbit\RabbitMQFactory;
use Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker\Rabbit\RabbitMQSender;

class RabbitMQFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function senderModeReturnsRabbitMQSender(): void
    {
        $connection = $this->createMock(AMQPStreamConnection::class);
        $queue = 'test_queue';
        $mode = RabbitMQMode::SENDER;

        $result = RabbitMQFactory::create($connection, $queue, $mode);

        $this->assertInstanceOf(RabbitMQSender::class, $result);
    }

    /**
     * @test
     */
    public function consumerModeReturnsRabbitMQConsumer(): void
    {
        $connection = $this->createMock(AMQPStreamConnection::class);
        $queue = 'test_queue';
        $mode = RabbitMQMode::CONSUMER;

        $result = RabbitMQFactory::create($connection, $queue, $mode);

        $this->assertInstanceOf(RabbitMQConsumer::class, $result);
    }
}
