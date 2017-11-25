<?php

namespace Pucene\Component\Symfony\Pool;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CollectorCompilerPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    private $tagName;

    /**
     * @var string
     */
    private $poolServiceId;

    /**
     * @var string
     */
    private $aliasAttribute;

    public function __construct(string $tagName, string $poolServiceId, string $aliasAttribute = 'alias')
    {
        $this->tagName = $tagName;
        $this->poolServiceId = $poolServiceId;
        $this->aliasAttribute = $aliasAttribute;
    }

    public function process(ContainerBuilder $container)
    {
        if (!$container->has($this->poolServiceId)) {
            return;
        }

        $references = [];
        foreach ($container->findTaggedServiceIds($this->tagName) as $id => $tags) {
            foreach ($tags as $tag) {
                $references[$tag[$this->aliasAttribute]] = $id;
            }
        }

        $container->getDefinition($this->poolServiceId)->replaceArgument(0, $references);
    }
}
