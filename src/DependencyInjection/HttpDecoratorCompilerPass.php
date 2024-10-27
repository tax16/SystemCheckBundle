<?php

namespace Tax16\SystemCheckBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tax16\SystemCheckBundle\Services\Health\Checker\Decorator\HttpServiceCheckerDecorator;
use Tax16\SystemCheckBundle\Services\Health\Checker\HttpServiceCheckInterface;

class HttpDecoratorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $taggedServices = $container->findTaggedServiceIds('system_check.health_check_trace');

        foreach ($taggedServices as $id => $tags) {
            $serviceDefinition = $container->getDefinition($id);
            $class = $serviceDefinition->getClass();

            if (!$class || !is_subclass_of($class, HttpServiceCheckInterface::class)) {
                throw new \InvalidArgumentException(sprintf('Service "%s" must implement "%s".', $id, HttpServiceCheckInterface::class));
            }
            $decoratedServiceId = $id.'_trace_decorator';

            $container->register($decoratedServiceId, HttpServiceCheckerDecorator::class)
                ->setDecoratedService($id, null, 2)
                ->setAutowired(true)
                ->setPublic(false);
        }
    }
}
