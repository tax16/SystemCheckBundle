<?php

namespace Tax16\SystemCheckBundle\Services\Health\Enum;

enum RabbitMQMode: string
{
    case SENDER = 'sender';
    case CONSUMER = 'consumer';
    case PING = 'ping';
}
