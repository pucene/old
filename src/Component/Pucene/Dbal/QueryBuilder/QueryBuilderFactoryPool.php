<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder;

class QueryBuilderFactoryPool implements QueryBuilderFactoryPoolInterface
{
    /**
     * @var QueryBuilderFactoryInterface[]
     */
    private $builders = [];

    /**
     * @param QueryBuilderFactoryInterface[] $builders
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
