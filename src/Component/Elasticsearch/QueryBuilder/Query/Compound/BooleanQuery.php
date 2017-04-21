<?php

namespace Pucene\Component\Elasticsearch\QueryBuilder\Query\Compound;

use Pucene\Component\Elasticsearch\QueryBuilder\QueryBuilderPool;
use Pucene\Component\Elasticsearch\QueryBuilder\QueryBuilderPoolInterface;
use Pucene\Component\Elasticsearch\QueryBuilder\QueryInterface;
use Pucene\Component\QueryBuilder\Query\Compound\Boolean;

/**
 * Represents bool query.
 */
class BooleanQuery implements QueryInterface
{
    /**
     * @var Boolean
     */
    private $query;

    /**
     * @var QueryBuilderPoolInterface
     */
    private $queryBuilderPool;

    /**
     * @param Boolean $query
     * @param QueryBuilderPoolInterface $queryBuilderPool
     */
    public function __construct(Boolean $query, QueryBuilderPoolInterface $queryBuilderPool)
    {
        $this->query = $query;
        $this->queryBuilderPool = $queryBuilderPool;
    }

    public function toArray()
    {
        $parameter = [];

        if (0 < count($this->query->getShouldQueries())) {
            $parameter['should'] = [];
            foreach ($this->query->getShouldQueries() as $query) {
                $parameter['should'][] = $this->queryBuilderPool->get(get_class($query))->build($query)->toArray();
            }
        }

        return ['bool' => $parameter];
    }
}
