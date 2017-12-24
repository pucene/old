<?php

namespace Pucene\Tests\TestBundle\DependencyInjection;

use Pucene\Component\Mapping\Types;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * TODO add description here.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('pucene')
            ->children()
                ->append($this->getAdaptersNode())
                ->append($this->getIndexNode())
            ->end();

        return $treeBuilder;
    }

    private function getAdaptersNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('adapters')
            ->isRequired()
            ->children()
                ->arrayNode('pucene')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('doctrine_dbal_connection')->defaultValue('doctrine.dbal.default_connection')->end()
                    ->end()
                ->end()
                ->arrayNode('elasticsearch')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('hosts')
                            ->defaultValue(['localhost:9200'])
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('settings')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('number_of_shards')->defaultValue(1)->end()
                                ->scalarNode('number_of_replicas')->defaultValue(1)->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }

    private function getIndexNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('indices')
            ->useAttributeAsKey('name')
            ->isRequired()
            ->prototype('array')
                ->children()
                    ->arrayNode('analysis')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('character_filters')->end()
                            ->arrayNode('token_filters')->end()
                            ->arrayNode('tokenizers')->end()
                            ->arrayNode('analyzers')->end()
                        ->end()
                    ->end()
                    ->arrayNode('mappings')
                        ->prototype('array')
                            ->append($this->getProperties())
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }

    private function getProperties($i = 5)
    {
        $builder = new TreeBuilder();
        $node = $builder->root('properties');

        $children = $node
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->children()
                    ->enumNode('type')
                        ->values(Types::getTypes())
                        ->defaultValue(Types::TEXT)
                    ->end()
                    ->scalarNode('analyzer')
                        ->defaultValue(null)
                    ->end()
                    ->arrayNode('fields')
                        ->prototype('array')
                            ->children()
                                ->enumNode('type')
                                    ->values(Types::getTypes())
                                    ->defaultValue(Types::TEXT)
                                ->end()
                                ->scalarNode('analyzer')
                                    ->defaultValue(null)
                                ->end()
                            ->end()
                        ->end()
                    ->end();

        if ($i > 0) {
            $children->append($this->getProperties($i - 1));
        }

        return $node;
    }
}
