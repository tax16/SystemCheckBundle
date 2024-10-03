<?php

namespace Tax16\SystemCheckBundle\Enum;

enum RabbitMQMode: string
{
    case SENDER = 'sender';
    case CONSUMER = 'consumer';
    case PING = 'ping';
}
