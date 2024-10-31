<?php

namespace Tax16\SystemCheckBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tax16\SystemCheckBundle\Enum\CriticalityLevel;
use Tax16\SystemCheckBundle\Services\Health\Checker\ServiceCheckInterface;
use Tax16\SystemCheckBundle\Services\Health\HealthCheckManager;

class HealthCheckCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(HealthCheckManager::class)) {
            return;
        }

        $definition = $container->findDefinition(HealthCheckManager::class);

        $taggedServices = $container->findTaggedServiceIds('system_check.health_check');

        $healthChecks = [];
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $priorityValue = $attributes['priority'] ?? CriticalityLevel::LOW->value;

                if (!CriticalityLevel::isValid($priorityValue)) {
                    $priorityValue = CriticalityLevel::LOW->value;
                }

                $serviceDefinition = $container->getDefinition($id);
                $class = $serviceDefinition->getClass();
                if (!$class || !is_subclass_of($class, ServiceCheckInterface::class)) {
                    throw new \InvalidArgumentException(sprintf('Service "%s" must implement "%s".', $id, ServiceCheckInterface::class));
                }

                $healthChecks[] = [
                    'service' => new Reference($id),
                    'label' => $attributes['label'] ?? 'unknown',
                    'priority' => $priorityValue,
                    'description' => $attributes['description'] ?? 'No description',
                ];
            }
        }

        $definition->setArgument(0, $healthChecks);
    }
}
