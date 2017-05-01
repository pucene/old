<?php

namespace Pucene\Component\Elasticsearch\QueryBuilder\Query\Compound;

use Pucene\Component\Elasticsearch\QueryBuilder\QueryBuilderPoolInterface;
use Pucene\Component\Elasticsearch\QueryBuilder\QueryInterface;
use Pucene\Component\QueryBuilder\Query\Compound\BoolQuery;

/**
 * Represents bool query.
 */
class BoolBuilder implements QueryInterface
{
    /**
     * @var BoolQuery
     */
    private $query;

    /**
     * @var QueryBuilderPoolInterface
     */
    private $queryBuilderPool;

    /**
     * @param BoolQuery $query
     * @param QueryBuilderPoolInterface $queryBuilderPool
     */
    public function __construct(BoolQuery $query, QueryBuilderPoolInterface $queryBuilderPool)
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

        if (0 < count($this->query->getFilterQueries())) {
            $parameter['filter'] = [];
            foreach ($this->query->getFilterQueries() as $query) {
                $parameter['filter'][] = $this->queryBuilderPool->get(get_class($query))->build($query)->toArray();
            }
        }

        if (0 < count($this->query->getMustNotQueries())) {
            $parameter['must_not'] = [];
            foreach ($this->query->getMustNotQueries() as $query) {
                $parameter['must_not'][] = $this->queryBuilderPool->get(get_class($query))->build($query)->toArray();
            }
        }

        if (0 < count($this->query->getMustQueries())) {
            $parameter['must'] = [];
            foreach ($this->query->getMustQueries() as $query) {
                $parameter['must'][] = $this->queryBuilderPool->get(get_class($query))->build($query)->toArray();
            }
        }

        return ['bool' => $parameter];
    }
}
