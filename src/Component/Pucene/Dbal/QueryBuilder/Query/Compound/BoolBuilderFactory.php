<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Query\Compound;

use Pucene\Component\Pucene\Dbal\DbalStorage;
use Pucene\Component\Pucene\Dbal\QueryBuilder\QueryBuilderInterface;
use Pucene\Component\Pucene\Dbal\QueryBuilder\QueryBuilderPoolInterface;
use Pucene\Component\QueryBuilder\Query\Compound\BoolQuery;
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

        return new BoolBuilder($shouldQueries, $storage->getSchema(), $storage->getConnection());
    }
}
