<?php

namespace Tax16\SystemCheckBundle\Core\Domain\Enum;

class CacheType extends Enum
{
    public const REDIS = 'redis';
    public const MEMCACHED = 'memcached';
}