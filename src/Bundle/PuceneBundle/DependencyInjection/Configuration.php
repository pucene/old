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
                                        ->children();
                                            $node = $this->getFieldMappingNode($node)
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

    private function getFieldMappingNode($node, $fields = true)
    {
        $node
            ->enumNode('type')
                ->values(Types::getTypes())
                ->defaultValue('text')
            ->end()
            ->scalarNode('analyzer')->end()
            ->booleanNode('index')->end()
            ->floatNode('boost')->end();

        if ($fields) {
            $node = $node
                ->arrayNode('fields')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children();
                            $node = $this->getFieldMappingNode($node, false)
                        ->end()
                    ->end()
                ->end();
        }

        $node = $node
            ->booleanNode('eager_global_ordinals')->end()
            ->booleanNode('fielddata')->end()
            ->arrayNode('fielddata_frequency_filter')
                ->children()
                    ->floatNode('min')->end()
                    ->floatNode('max')->end()
                    ->integerNode('min_segment_size')->end()
                ->end()
            ->end()
            ->booleanNode('include_in_all')->end()
            ->enumNode('index_options')
                ->values([
                    'docs',
                    'freqs',
                    'positions',
                    'offsets',
                ])
            ->end()
            ->booleanNode('norms')->end()
            ->integerNode('position_increment_gap')->end()
            ->booleanNode('store')->end()
            ->scalarNode('search_analyzer')->end()
            ->scalarNode('search_quote_analyzer')->end()
            ->enumNode('similarity')
                ->values([
                    'BM25',
                    'classic',
                    'boolean',
                ])
            ->end()
            ->enumNode('term_vector')
                ->values([
                    'no',
                    'yes',
                    'with_positions',
                    'with_offsets',
                    'with_positions_offsets',
                ])
            ->end()
            ->booleanNode('doc_values')->end()
            ->integerNode('ignore_above')->end()
            ->scalarNode('null_value')->end()
            ->scalarNode('normalizer')->end()
            ->booleanNode('coerce')->end()
            ->booleanNode('ignore_malformed')->end()
            ->scalarNode('format')->end();

        return $node;
    }
}
