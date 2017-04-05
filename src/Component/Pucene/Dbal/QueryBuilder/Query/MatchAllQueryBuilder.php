<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Query;

use Pucene\Component\Pucene\Dbal\QueryBuilder\QueryBuilderInterface;
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
