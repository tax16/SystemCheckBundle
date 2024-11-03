<?php

declare(strict_types=1);

namespace Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker;

use Tax16\SystemCheckBundle\Core\Domain\Constant\CheckerIcon;
use Tax16\SystemCheckBundle\Core\Domain\Enum\CacheType;
use Tax16\SystemCheckBundle\Core\Domain\Model\CheckInfo;
use Tax16\SystemCheckBundle\Core\Domain\Service\ServiceCheckInterface;

class CacheChecker implements ServiceCheckInterface
{
    /**
     * @var object|mixed
     */
    private $cacheClient;

    /**
     * @var string
     */
    private $cacheType;

    /**
     * @param mixed  $cacheClient The cache client (Redis, Memcached, etc.)
     * @param string $cacheType   the type of cache being checked
     */
    public function __construct($cacheClient, string $cacheType = CacheType::REDIS)
    {
        assert(CacheType::isValid($cacheType));
        $this->cacheType = $cacheType;
        $this->cacheClient = $cacheClient;
    }

    public function check(bool $withNetwork = false): CheckInfo
    {
        try {
            if (CacheType::REDIS === $this->cacheType) {
                if (!$this->cacheClient instanceof \Redis) {
                    throw new \InvalidArgumentException('Invalid Redis client.');
                }
                $this->cacheClient->ping();
            }

            return new CheckInfo(
                'Cache Health Check',
                true,
                sprintf('Connection to %s cache is successful.', $this->cacheType),
                null
            );
        } catch (\Exception $e) {
            return new CheckInfo(
                'Cache Health Check',
                false,
                sprintf('Failed to connect to %s cache: %s', $this->cacheType, $e->getMessage()),
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
        return CheckerIcon::CACHE;
    }

    public function isAllowedToHaveChildren(): bool
    {
        return false;
    }
}
