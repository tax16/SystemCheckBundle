<?php

namespace Tax16\SystemCheckBundle\Infrastructure\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Tax16\SystemCheckBundle\Core\Domain\Port\ConfigurationProviderInterface;

class ParameterBagAdapter implements ConfigurationProviderInterface
{
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function get(string $key)
    {
        return $this->parameterBag->get($key);
    }
}