<?php

namespace Pucene\Bundle\PuceneBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class QueryBuilderCompilerPass implements CompilerPassInterface
{
    const TAG_NAME = 'pucene.query_builder';
    const POOL_SERVICE_ID = 'pucene.query_builder.pool';
    const QUERY_ATTRIBUTE_NAME = 'query';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::POOL_SERVICE_ID)) {
            return;
        }

        $references = [];
        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $id => $tags) {
            foreach ($tags as $tag) {
                $references[$tag[self::QUERY_ATTRIBUTE_NAME]] = $id;
            }
        }

        $container->getDefinition(self::POOL_SERVICE_ID)->replaceArgument(0, $references);
    }
}
