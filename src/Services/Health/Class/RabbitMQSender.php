<?php

namespace Tax16\SystemCheckBundle\Services\Health\Class;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQSender
{
    private AMQPStreamConnection $connection;
    private string $queue;

    public function __construct(AMQPStreamConnection $connection, string $queue)
    {
        $this->connection = $connection;
        $this->queue = $queue;
    }

    public function sendMessage(string $message): void
    {
        $channel = $this->connection->channel();
        $channel->queue_declare($this->queue, false, true, false, false);

        $msg = new AMQPMessage($message);
        $channel->basic_publish($msg, '', $this->queue);

        $channel->close();
        $this->connection->close();
    }
}
