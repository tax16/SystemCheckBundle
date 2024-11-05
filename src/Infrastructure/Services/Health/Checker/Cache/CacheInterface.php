<?php

namespace Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker\Cache;

interface CacheInterface
{
    public function ping(): void;
}
