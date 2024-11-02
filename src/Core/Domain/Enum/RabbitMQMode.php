<?php

namespace Tax16\SystemCheckBundle\Core\Domain\Enum;

class RabbitMQMode extends Enum
{
    public const SENDER = 'sender';
    public const CONSUMER = 'consumer';
    public const PING = 'ping';
}