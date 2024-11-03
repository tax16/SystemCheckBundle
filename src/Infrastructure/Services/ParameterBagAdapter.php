<?php

declare(strict_types=1);

namespace Tax16\SystemCheckBundle\Infrastructure\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Tax16\SystemCheckBundle\Core\Domain\Port\ConfigurationProviderInterface;

class ParameterBagAdapter implements ConfigurationProviderInterface
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     * @return array<mixed>|bool|float|int|string|null
     */
    public function get(string $key)
    {
        return $this->parameterBag->get($key);
    }
}
