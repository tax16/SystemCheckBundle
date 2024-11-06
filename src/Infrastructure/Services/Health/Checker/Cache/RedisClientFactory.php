<?php

namespace Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker\Cache;

use Redis;

class RedisClientFactory implements CacheInterface
{
    /**
     * @var \Redis
     */
    private $redisClient;

    /**
     * Create and return a Redis client instance based on the provided Redis URL.
     *
     * @throws \InvalidArgumentException if the Redis URL is invalid
     */
    public function __invoke(string $redisUrl): CacheInterface
    {
        $urlComponents = parse_url($redisUrl);
        if (false === $urlComponents || !isset($urlComponents['host'], $urlComponents['port'])) {
            throw new \InvalidArgumentException('Invalid Redis URL format.');
        }

        $this->redisClient = new \Redis();

        if (isset($urlComponents['user'], $urlComponents['pass'])) {
            $this->redisClient->auth($urlComponents['pass']);
        }

        $this->redisClient->connect($urlComponents['host'], $urlComponents['port']);

        return $this;
    }

    public function ping(): void
    {
        if (!$this->redisClient->ping()) {
            throw new \RuntimeException('Failed to ping Redis server.');
        }
    }
}
