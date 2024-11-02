<?php

namespace Tax16\SystemCheckBundle\Infrastructure\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tax16\SystemCheckBundle\Core\Application\Service\HealthCheckProcessor;
use Tax16\SystemCheckBundle\Core\Domain\Enum\CriticalityLevel;
use Tax16\SystemCheckBundle\Core\Domain\Service\ServiceCheckInterface;

class HealthCheckCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(HealthCheckProcessor::class)) {
            return;
        }

        $definition = $container->findDefinition(HealthCheckProcessor::class);
        $taggedServices = $container->findTaggedServiceIds('system_check.health_check');

        $healthChecks = [];
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $priorityValue = CriticalityLevel::LOW;

                if (CriticalityLevel::isValid($attributes['priority'])) {
                    $priorityValue = $attributes['priority'];
                }

                $serviceDefinition = $container->getDefinition($id);
                $class = $serviceDefinition->getClass();
                if (!$class || !is_subclass_of($class, ServiceCheckInterface::class)) {
                    throw new \InvalidArgumentException(sprintf('Service "%s" must implement "%s".', $id, ServiceCheckInterface::class));
                }

                if ($attributes['parent'] ?? false) {
                    if ($id === $attributes['parent']) {
                        throw new \InvalidArgumentException(sprintf('Service parent "%s" should be different of current id', $attributes['parent']));
                    }

                    $serviceDefinition = $container->getDefinition($attributes['parent']);
                    $classParent = $serviceDefinition->getClass();

                    if (!$classParent || !is_subclass_of($classParent, ServiceCheckInterface::class)) {
                        throw new \InvalidArgumentException(sprintf('Parent Service "%s" must implement "%s".', $id, ServiceCheckInterface::class));
                    }
                }

                $healthChecks[] = [
                    'service' => new Reference($id),
                    'label' => $attributes['label'] ?? 'unknown',
                    'priority' => $priorityValue,
                    'description' => $attributes['description'] ?? 'No description',
                    'execute' => $attributes['execute'] ?? true,
                    'parent' => $attributes['parent'] ?? null,
                    'id' => $id,
                ];
            }
        }

        $definition->setArgument(0, $healthChecks);
    }
}
