<?php

namespace Tax16\SystemCheckBundle\Services\Health\Checker;

use Tax16\SystemCheckBundle\DTO\CheckResult;
use Tax16\SystemCheckBundle\Enum\CacheType;
use Tax16\SystemCheckBundle\Services\Health\Checker\Constant\CheckerIcon;

class CacheChecker implements ServiceCheckInterface
{
    private mixed $cacheClient;
    private CacheType $cacheType;

    /**
     * @param mixed     $cacheClient The cache client (Redis, Memcached, etc.)
     * @param CacheType $cacheType   the type of cache being checked
     */
    public function __construct(mixed $cacheClient, CacheType $cacheType = CacheType::Redis)
    {
        $this->cacheClient = $cacheClient;
        $this->cacheType = $cacheType;
    }

    public function check(): CheckResult
    {
        try {
            if (CacheType::Redis == $this->cacheType) {
                if (!$this->cacheClient instanceof \Redis) {
                    throw new \InvalidArgumentException('Invalid Redis client.');
                }
                $this->cacheClient->ping();
            }

            return new CheckResult(
                'Cache Health Check',
                true,
                sprintf('Connection to %s cache is successful.', $this->cacheType->value),
                null
            );
        } catch (\Exception $e) {
            return new CheckResult(
                'Cache Health Check',
                false,
                sprintf('Failed to connect to %s cache: %s', $this->cacheType->value, $e->getMessage()),
                $e->getTraceAsString()
            );
        }
    }

    public function getName(): string
    {
        return 'Cache Health';
    }

    public function getIcon(): ?string
    {
        return CheckerIcon::GOOGlE_CHROME;
    }
}
