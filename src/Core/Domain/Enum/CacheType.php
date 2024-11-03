<?php
declare(strict_types=1);

namespace Tax16\SystemCheckBundle\Core\Domain\Enum;

class CacheType extends Enum
{
    public const REDIS = 'redis';
    public const MEMCACHED = 'memcached';
}