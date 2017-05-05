<?php

namespace Pucene\Tests\TestBundle;

use Pucene\Tests\TestBundle\DependencyInjection\CompilerPass\CollectorCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class TestBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(
            new CollectorCompilerPass(
                'pucene.pucene.visitor',
                'pucene.pucene.visitor_pool'
            )
        );
        $container->addCompilerPass(
            new CollectorCompilerPass(
                'pucene.pucene.interpreter',
                'pucene.pucene.interpreter_pool',
                'element'
            )
        );
        $container->addCompilerPass(
            new CollectorCompilerPass(
                'pucene.elasticsearch.query_builder',
                'pucene.elasticsearch.query_builder.pool'
            )
        );
    }
}
