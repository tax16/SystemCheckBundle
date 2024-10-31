<?php

namespace Tax16\SystemCheckBundle\Enum;

enum CacheType: string
{
    case Redis = 'redis';

    case Memcached = 'memcached';
}
