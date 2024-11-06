<?php

namespace Tax16\SystemCheckBundle\Infrastructure\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Kernel;
use Tax16\SystemCheckBundle\Core\Domain\Port\ConfigurationProviderInterface;

class SetConfigurationProviderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (version_compare(Kernel::VERSION, '4.0', '>=')) {
            $container->register(
                'configuration_provider_interface.parameter_bag',
                'Tax16\SystemCheckBundle\Infrastructure\Services\ParameterBagAdapter'
            )
                ->addArgument(new Reference('parameter_bag'));
            $container->setAlias(
                ConfigurationProviderInterface::class,
                'configuration_provider_interface.parameter_bag'
            );
        } else {
            $container->register(
                'configuration_provider_interface.container',
                'Tax16\SystemCheckBundle\Infrastructure\Services\ParameterBagAdapter'
            )
                ->addArgument(new Reference('service_container'));

            $container->setAlias(
                ConfigurationProviderInterface::class,
                'configuration_provider_interface.container'
            );
        }
    }
}
