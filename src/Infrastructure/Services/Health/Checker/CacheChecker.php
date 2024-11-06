<?php

declare(strict_types=1);

namespace Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker;

use Tax16\SystemCheckBundle\Core\Domain\Constant\CheckerIcon;
use Tax16\SystemCheckBundle\Core\Domain\Enum\CacheType;
use Tax16\SystemCheckBundle\Core\Domain\Model\CheckInfo;
use Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker\Cache\CacheFactory;
use Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker\Cache\CacheInterface;

class CacheChecker extends AbstractChecker
{
    /**
     * @var CacheInterface
     */
    private $cacheClient;

    /**
     * @var string
     */
    private $cacheType;

    /**
     * @var string
     */
    private $cacheUrl;

    /**
     * @param string $cacheUrl  The Redis connection URL
     * @param string $cacheType The type of cache being checked
     */
    public function __construct(string $cacheUrl, string $cacheType = CacheType::REDIS)
    {
        parent::__construct('Cache Health', CheckerIcon::CACHE);

        assert(CacheType::isValid($cacheType));
        $this->cacheType = $cacheType;
        $this->cacheUrl = $cacheUrl;
    }

    public function getCacheFactory(): CacheFactory
    {
        return new CacheFactory();
    }

    public function check(bool $withNetwork = false): CheckInfo
    {
        try {
            $this->cacheClient = $this->getCacheFactory()->createClient($this->cacheType, $this->cacheUrl);
            $this->cacheClient->ping();

            return new CheckInfo(
                'Cache check',
                true,
                sprintf('Connection to %s cache is successful.', $this->cacheType),
                null
            );
        } catch (\Exception $e) {
            return new CheckInfo(
                'Cache check',
                false,
                sprintf('Failed to connect to %s cache: %s', $this->cacheType, $e->getMessage()),
                $e->getTraceAsString()
            );
        }
    }
}
