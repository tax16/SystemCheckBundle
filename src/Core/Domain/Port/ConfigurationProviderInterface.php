<?php

declare(strict_types=1);

namespace Tax16\SystemCheckBundle\Core\Domain\Port;

interface ConfigurationProviderInterface
{
    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key);
}