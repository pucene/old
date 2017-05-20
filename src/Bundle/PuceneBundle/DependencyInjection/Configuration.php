<?php

namespace Pucene\Bundle\PuceneBundle\DependencyInjection;

use Pucene\Component\Mapping\Types;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Defines configuration for pucene/doctrine-bundle.
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
                ->enumNode('adapter')->isRequired()->values(['elasticsearch', 'pucene'])->end()
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
                            ->children()
                                ->arrayNode('properties')
                                    ->useAttributeAsKey('name')
                                    ->prototype('array')
                                        ->children()
                                            ->enumNode('type')
                                                ->values(Types::getTypes())
                                                ->defaultValue('text')
                                            ->end()
                                            ->scalarNode('analyzer')->defaultValue('standard')->end()
                                            ->scalarNode('format')->defaultValue('string')->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
