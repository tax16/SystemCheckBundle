<?php

namespace Tax16\SystemCheckBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tax16\SystemCheckBundle\DependencyInjection\HealthCheckCompilerPass;
use Tax16\SystemCheckBundle\DependencyInjection\HttpDecoratorCompilerPass;
use Tax16\SystemCheckBundle\DependencyInjection\ServiceCheckDecoratorCompilerPass;

class SystemCheckBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new HealthCheckCompilerPass());
        $container->addCompilerPass(new HttpDecoratorCompilerPass());
        $container->addCompilerPass(new ServiceCheckDecoratorCompilerPass());
    }
}
