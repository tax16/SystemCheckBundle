<?php

declare(strict_types=1);

namespace Tax16\SystemCheckBundle\Infrastructure\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker\Decorator\ServiceCheckDecorator;

class ServiceCheckDecoratorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $taggedServices = $container->findTaggedServiceIds('system_check.health_check');

        foreach ($taggedServices as $id => $tags) {
            $decoratedServiceId = $id.'.decorator';

            $container->register($decoratedServiceId, ServiceCheckDecorator::class)
                ->setDecoratedService($id)
                ->setArgument('$decoratedService', new Reference($decoratedServiceId.'.inner'))
                ->setAutowired(true)
                ->setPublic(true);
        }
    }
}
