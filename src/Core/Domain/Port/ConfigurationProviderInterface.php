<?php

declare(strict_types=1);

namespace Tax16\SystemCheckBundle\Core\Domain\Port;

interface ConfigurationProviderInterface
{
    /**
     * @return array<mixed>|bool|float|int|string|null
     */
    public function get(string $key);
}
