<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Query\TermLevel;

use Pucene\Component\Pucene\Dbal\DbalStorage;
use Pucene\Component\Pucene\Dbal\QueryBuilder\QueryBuilderInterface;
use Pucene\Component\QueryBuilder\Query\QueryInterface;

/**
 * Builder for term query.
 */
class TermQueryBuilder implements QueryBuilderInterface
{
    public function build(QueryInterface $query, DbalStorage $storage)
    {
        return new TermQuery($query->getField(), $query->getTerm());
    }
}
