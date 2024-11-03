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
        $rootNode = $treeBuilder->getRootNode();

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