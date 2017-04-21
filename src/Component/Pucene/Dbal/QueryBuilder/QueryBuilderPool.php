<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder;

class QueryBuilderPool implements QueryBuilderPoolInterface
{
    /**
     * @var QueryBuilderInterface[]
     */
    private $builders = [];

    /**
     * @param QueryBuilderInterface[] $builders
     */
    public function __construct(array $builders)
    {
        $this->builders = $builders;
    }

    public function get($className)
    {
        return $this->builders[$className];
    }
}
