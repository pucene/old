<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Query\TermLevel;

use Pucene\Component\Pucene\Dbal\QueryBuilder\QueryBuilderInterface;
use Pucene\Component\QueryBuilder\Query\QueryInterface;

/**
 * Builder for term query.
 */
class TermQueryBuilder implements QueryBuilderInterface
{
    public function build(QueryInterface $query)
    {
        return new TermQuery($query);
    }
}
