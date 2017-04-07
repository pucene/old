<?php

namespace Pucene\Tests\TestBundle;

use Pucene\Tests\TestBundle\DependencyInjection\CompilerPass\QueryBuilderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * TODO add description here.
 */
class TestBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(
            new QueryBuilderCompilerPass(
                'pucene.pucene.query_builder',
                'pucene.pucene.query_builder.pool'
            )
        );
        $container->addCompilerPass(
            new QueryBuilderCompilerPass(
                'pucene.elasticsearch.query_builder',
                'pucene.elasticsearch.query_builder.pool'
            )
        );
    }
}
