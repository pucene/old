<?php

namespace Pucene\Tests\TestBundle;

use Pucene\Component\Symfony\Pool\CollectorCompilerPass;
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
                'pucene.pucene.visitor_pool',
                'query'
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
                'pucene.elasticsearch.visitor',
                'pucene.elasticsearch.visitor_pool',
                'query'
            )
        );
    }
}
