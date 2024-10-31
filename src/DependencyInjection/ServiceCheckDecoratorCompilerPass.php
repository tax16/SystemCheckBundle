<?php

namespace Tax16\SystemCheckBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tax16\SystemCheckBundle\Services\Health\Checker\Decorator\ServiceCheckDecorator;

class ServiceCheckDecoratorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $taggedServices = $container->findTaggedServiceIds('system_check.health_check');

        foreach ($taggedServices as $id => $tags) {
            $decoratedServiceId = $id.'.decorator';

            $container->register($decoratedServiceId, ServiceCheckDecorator::class)
                ->setDecoratedService($id)
                ->setAutowired(true)
                ->setPublic(true);
        }
    }
}
