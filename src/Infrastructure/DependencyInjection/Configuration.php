<?php

declare(strict_types=1);

namespace Tax16\SystemCheckBundle\Infrastructure\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('system_check');

        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            $rootNode = $treeBuilder->root('system_check'); // @phpstan-ignore-line
        }

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('id')
            ->defaultNull()
            ->end()
            ->scalarNode('name')
            ->defaultValue('APPLICATION_NAME')
            ->end()
            ->end();

        return $treeBuilder;
    }
}
