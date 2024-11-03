<?php

namespace unit\Infrastructure\Services\Health\Rabbit;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker\Rabbit\RabbitMQConsumer;

class RabbitMQConsumerTest extends TestCase
{
    /**
     * @test
     */
    public function successfulMessageConsumption(): void
    {
        $connection = $this->createMock(AMQPStreamConnection::class);
        $channel = $this->createMock(AMQPChannel::class);
        $message = $this->createMock(AMQPMessage::class);
        $connection->method('channel')->willReturn($channel);
        $channel->method('queue_declare');
        $channel->method('basic_consume')->willReturnCallback(function ($queue, $consumer_tag, $no_local, $no_ack, $exclusive, $nowait, $callback) use ($message) {
            $callback($message);
        });
        $channel->method('is_consuming')->willReturnOnConsecutiveCalls(true, false);
        $channel->method('wait');
        $channel->method('close');
        $connection->method('close');

        $consumer = new RabbitMQConsumer($connection, 'test_queue');
        $consumer->consumeMessage();
    }

    /**
     * @test
     */
    public function noMessageToConsume(): void
    {
        $connection = $this->createMock(AMQPStreamConnection::class);
        $channel = $this->createMock(AMQPChannel::class);

        $connection->method('channel')->willReturn($channel);
        $channel->method('queue_declare');
        $channel->method('basic_consume')->willReturnCallback(function ($queue, $consumer_tag, $no_local, $no_ack, $exclusive, $nowait, $callback) {
            // No message to consume
        });
        $channel->method('is_consuming')->willReturnOnConsecutiveCalls(true, false);
        $channel->method('wait');
        $channel->method('close');
        $connection->method('close');

        $consumer = new RabbitMQConsumer($connection, 'test_queue');
        $result = $consumer->consumeMessage();

        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function connectionFailure(): void
    {
        $connection = $this->createMock(AMQPStreamConnection::class);
        $connection->method('channel')->will($this->throwException(new \Exception('Connection error')));

        $consumer = new RabbitMQConsumer($connection, 'test_queue');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Connection error');

        $consumer->consumeMessage();
    }
}
