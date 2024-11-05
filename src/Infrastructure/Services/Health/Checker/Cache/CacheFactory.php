<?php

namespace Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker\Cache;

class CacheFactory
{
    /**
     * Create a cache client based on the specified type and URL.
     *
     * @param string $cacheType The type of cache (e.g., 'redis' or 'memcached')
     * @param string $cacheUrl  The connection URL for the cache
     *
     * @throws \InvalidArgumentException
     */
    public function createClient(string $cacheType, string $cacheUrl): CacheInterface
    {
        switch (strtolower($cacheType)) {
            case 'redis':
                return (new RedisClientFactory())($cacheUrl);
            default:
                throw new \InvalidArgumentException("Unsupported cache type: $cacheType");
        }
    }
}
