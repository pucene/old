<?php

namespace Pucene\Component\Elasticsearch\QueryBuilder\Query\FullText;

use Pucene\Component\Elasticsearch\QueryBuilder\QueryBuilderInterface;
use Pucene\Component\QueryBuilder\Query\QueryInterface;

/**
 * Build match query.
 */
class MatchQueryBuilder implements QueryBuilderInterface
{
    public function build(QueryInterface $query)
    {
        return new MatchQuery($query);
    }
}
