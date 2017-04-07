<?php

namespace Pucene\Tests\TestBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class QueryBuilderCompilerPass implements CompilerPassInterface
{
    const QUERY_ATTRIBUTE_NAME = 'query';

    /**
     * @var string
     */
    private $tagName;

    /**
     * @var string
     */
    private $poolServiceId;

    /**
     * @param string $tagName
     * @param string $poolServiceId
     */
    public function __construct($tagName, $poolServiceId)
    {
        $this->tagName = $tagName;
        $this->poolServiceId = $poolServiceId;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has($this->poolServiceId)) {
            return;
        }

        $references = [];
        foreach ($container->findTaggedServiceIds($this->tagName) as $id => $tags) {
            foreach ($tags as $tag) {
                $references[$tag[self::QUERY_ATTRIBUTE_NAME]] = new Reference($id);
            }
        }

        $container->getDefinition($this->poolServiceId)->replaceArgument(0, $references);
    }
}
