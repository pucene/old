<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Query\Compound;

use Pucene\Component\Pucene\Dbal\DbalStorage;
use Pucene\Component\Pucene\Dbal\QueryBuilder\QueryBuilderFactoryInterface;
use Pucene\Component\Pucene\Dbal\QueryBuilder\QueryBuilderFactoryPoolInterface;
use Pucene\Component\QueryBuilder\Query\Compound\BoolQuery;
use Pucene\Component\QueryBuilder\Query\QueryInterface;

/**
 * Build bool query.
 */
class BoolBuilderFactory implements QueryBuilderFactoryInterface
{
    /**
     * @var QueryBuilderFactoryPoolInterface
     */
    private $queryBuilderPool;

    /**
     * @param QueryBuilderFactoryPoolInterface $queryBuilderPool
     */
    public function __construct(QueryBuilderFactoryPoolInterface $queryBuilderPool)
    {
        $this->queryBuilderPool = $queryBuilderPool;
    }

    /**
     * {@inheritdoc}
     *
     * @param BoolQuery $query
     */
    public function build(QueryInterface $query, DbalStorage $storage)
    {
        $shouldQueries = array_map(
            function ($shouldQuery) use ($storage) {
                return $this->queryBuilderPool->get(get_class($shouldQuery))->build($shouldQuery, $storage);
            },
            $query->getShouldQueries()
        );

        $mustQueries = array_map(
            function ($shouldQuery) use ($storage) {
                return $this->queryBuilderPool->get(get_class($shouldQuery))->build($shouldQuery, $storage);
            },
            $query->getMustQueries()
        );

        $mustNotQueries = array_map(
            function ($shouldQuery) use ($storage) {
                return $this->queryBuilderPool->get(get_class($shouldQuery))->build($shouldQuery, $storage);
            },
            $query->getMustNotQueries()
        );

        $filterQueries = array_map(
            function ($shouldQuery) use ($storage) {
                return $this->queryBuilderPool->get(get_class($shouldQuery))->build($shouldQuery, $storage);
            },
            $query->getFilterQueries()
        );

        return new BoolBuilder(
            $shouldQueries,
            $mustQueries,
            $mustNotQueries,
            $filterQueries,
            $storage->getSchema(),
            $storage->getConnection()
        );
    }
}
