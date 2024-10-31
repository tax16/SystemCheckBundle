<?php

namespace Tax16\SystemCheckBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tax16\SystemCheckBundle\Services\Health\Checker\Decorator\HttpServiceCheckerDecorator;
use Tax16\SystemCheckBundle\Services\Health\Checker\HttpServiceCheckInterface;

class HttpDecoratorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('system_check.id')) {
            throw new \InvalidArgumentException('The parameter "system_check.id" is not defined.');
        }

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
                ->setAutowired(false)
                ->setPublic(false)
                ->addArgument(new Reference($decoratedServiceId . '.inner'))
                ->addArgument(new Reference('parameter_bag'))
                ->addArgument(new Reference('request_stack'));
        }
    }
}
