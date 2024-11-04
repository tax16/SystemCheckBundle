<?php

declare(strict_types=1);

namespace Tax16\SystemCheckBundle\Infrastructure\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Tax16\SystemCheckBundle\Core\Domain\Port\ConfigurationProviderInterface;

class ParameterBagAdapter implements ConfigurationProviderInterface
{
    /**
     * @var ParameterBagInterface|ContainerInterface
     */
    private $parameters;

    /**
     * @param ParameterBagInterface|ContainerInterface $parameterSource
     */
    public function __construct($parameterSource)
    {
        $this->parameters = $parameterSource;
    }

    /**
     * @return array<mixed>|bool|float|int|string|null
     */
    public function get(string $key)
    {
        if ($this->parameters instanceof ParameterBagInterface) {
            return $this->parameters->get($key);
        }

        // Fallback for ContainerInterface (Symfony 3.4)
        if ($this->parameters instanceof ContainerInterface) {
            return $this->parameters->getParameter($key);
        }

        throw new \RuntimeException('Invalid parameter source provided.');
    }
}
