<?php

namespace Pucene\Component\Elasticsearch\QueryBuilder\Query\Compound;

use Pucene\Component\Elasticsearch\QueryBuilder\QueryBuilderInterface;
use Pucene\Component\Elasticsearch\QueryBuilder\QueryBuilderPoolInterface;
use Pucene\Component\QueryBuilder\Query\QueryInterface;

/**
 * Build bool query.
 */
class BoolBuilderFactory implements QueryBuilderInterface
{
    /**
     * @var QueryBuilderPoolInterface
     */
    private $queryBuilderPool;

    /**
     * @param QueryBuilderPoolInterface $queryBuilderPool
     */
    public function __construct(QueryBuilderPoolInterface $queryBuilderPool)
    {
        $this->queryBuilderPool = $queryBuilderPool;
    }

    public function build(QueryInterface $query)
    {
        return new BoolBuilder($query, $this->queryBuilderPool);
    }
}
