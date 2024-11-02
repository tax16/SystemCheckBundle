<?php

namespace Tax16\SystemCheckBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tax16\SystemCheckBundle\Infrastructure\DependencyInjection\HealthCheckCompilerPass;
use Tax16\SystemCheckBundle\Infrastructure\DependencyInjection\HttpDecoratorCompilerPass;
use Tax16\SystemCheckBundle\Infrastructure\DependencyInjection\ServiceCheckDecoratorCompilerPass;
use Tax16\SystemCheckBundle\Infrastructure\DependencyInjection\SystemCheckExtension;

class SystemCheckBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new HealthCheckCompilerPass());
        $container->addCompilerPass(new HttpDecoratorCompilerPass());
        $container->addCompilerPass(new ServiceCheckDecoratorCompilerPass());
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        return new SystemCheckExtension();
    }
}
