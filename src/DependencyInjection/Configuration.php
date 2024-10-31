<?php

namespace Tax16\SystemCheckBundle\DependencyInjection;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('system_check');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('id')
                    ->defaultNull()
                    ->end()
                ->scalarNode('name')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->end()
                ->end();

        return $treeBuilder;
    }
}