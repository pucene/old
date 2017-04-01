<?php

namespace Pucene\Component\Elasticsearch\QueryBuilder\Query;

use Pucene\Component\Elasticsearch\QueryBuilder\QueryBuilderInterface;
use Pucene\Component\QueryBuilder\Query\QueryInterface;

/**
 * Builds match_all query.
 */
class MatchAllQueryBuilder implements QueryBuilderInterface
{
    public function build(QueryInterface $query)
    {
        return new MatchAllQuery($query);
    }
}
