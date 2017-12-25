<?php

namespace Pucene\Tests\TestBundle\DependencyInjection;

use Pucene\Bundle\PuceneBundle\DependencyInjection\Configuration as PuceneConfiguration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration extends PuceneConfiguration implements ConfigurationInterface
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
}
