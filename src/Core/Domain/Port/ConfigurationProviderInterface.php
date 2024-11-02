<?php

namespace Tax16\SystemCheckBundle\Core\Domain\Port;

interface ConfigurationProviderInterface
{
    public function get(string $key);
}