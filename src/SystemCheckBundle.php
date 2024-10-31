<?php

namespace Tax16\SystemCheckBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tax16\SystemCheckBundle\DependencyInjection\HealthCheckCompilerPass;

class SystemCheckBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new HealthCheckCompilerPass());
    }
}
