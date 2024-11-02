<?php

namespace Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker\Rabbit;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQConsumer
{
    private $connection;
    private $queue;

    public function __construct(AMQPStreamConnection $connection, string $queue)
    {
        $this->connection = $connection;
        $this->queue = $queue;
    }

    public function consumeMessage(): void
    {
        $channel = $this->connection->channel();
        $channel->queue_declare($this->queue, false, true, false, false);

        $callback = function ($msg) use (&$message) {
            $message = $msg->body;
        };

        $channel->basic_consume($this->queue, '', false, true, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $this->connection->close();
    }
}
