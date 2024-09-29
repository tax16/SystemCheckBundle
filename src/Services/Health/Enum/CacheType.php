<?php

namespace Tax16\SystemCheckBundle\Services\Health\Enum;

enum CacheType: string
{
    case Redis = 'redis';

    case Memcached = 'memcached';
}
