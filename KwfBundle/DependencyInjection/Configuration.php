<?php
namespace KwfBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kwf');

        $rootNode
            ->children()
                ->arrayNode('csrf_protection')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('ignore_paths')
                            ->prototype('scalar')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
