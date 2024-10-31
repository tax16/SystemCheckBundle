<?php

namespace unit\Services\Health\Class;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPUnit\Framework\TestCase;
use Tax16\SystemCheckBundle\Enum\RabbitMQMode;
use Tax16\SystemCheckBundle\Services\Health\Checker\Class\RabbitMQConsumer;
use Tax16\SystemCheckBundle\Services\Health\Checker\Class\RabbitMQFactory;
use Tax16\SystemCheckBundle\Services\Health\Checker\Class\RabbitMQSender;

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
