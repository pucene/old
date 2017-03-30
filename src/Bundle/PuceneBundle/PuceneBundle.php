<?php

namespace Pucene\Bundle\PuceneBundle;

use Pucene\Bundle\PuceneBundle\DependencyInjection\CompilerPass\QueryBuilderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Entry-point of pucene-bundle.
 */
class PuceneBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new QueryBuilderCompilerPass());
    }
}
