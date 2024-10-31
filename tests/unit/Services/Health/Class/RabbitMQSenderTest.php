<?php

namespace unit\Services\Health\Class;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPUnit\Framework\TestCase;
use Tax16\SystemCheckBundle\Services\Health\Checker\Class\RabbitMQSender;

class RabbitMQSenderTest extends TestCase
{
    /**
     * @test
     */
    public function messageIsSentSuccessfully(): void
    {
        $connection = $this->createMock(AMQPStreamConnection::class);
        $channel = $this->createMock(AMQPChannel::class);

        $connection->method('channel')->willReturn($channel);
        $channel->method('queue_declare');
        $channel->method('basic_publish');
        $channel->method('close');
        $connection->method('close');

        $sender = new RabbitMQSender($connection, 'test_queue');
        $sender->sendMessage('Test Message');

        $this->assertTrue(true); // If no exception is thrown, the test is successful
    }

    /**
     * @test
     */
    public function connectionFailure(): void
    {
        $connection = $this->createMock(AMQPStreamConnection::class);
        $connection->method('channel')->will($this->throwException(new \Exception('Connection error')));

        $sender = new RabbitMQSender($connection, 'test_queue');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Connection error');

        $sender->sendMessage('Test Message');
    }

    /**
     * @test
     */
    public function queueDeclarationFailure(): void
    {
        $connection = $this->createMock(AMQPStreamConnection::class);
        $channel = $this->createMock(AMQPChannel::class);

        $connection->method('channel')->willReturn($channel);
        $channel->method('queue_declare')->will($this->throwException(new \Exception('Queue declaration error')));

        $sender = new RabbitMQSender($connection, 'test_queue');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Queue declaration error');

        $sender->sendMessage('Test Message');
    }

    /**
     * @test
     */
    public function messagePublishingFailure(): void
    {
        $connection = $this->createMock(AMQPStreamConnection::class);
        $channel = $this->createMock(AMQPChannel::class);

        $connection->method('channel')->willReturn($channel);
        $channel->method('queue_declare');
        $channel->method('basic_publish')->will($this->throwException(new \Exception('Message publishing error')));

        $sender = new RabbitMQSender($connection, 'test_queue');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Message publishing error');

        $sender->sendMessage('Test Message');
    }
}
