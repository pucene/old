<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Query;

use Pucene\Component\Pucene\Dbal\DbalStorage;
use Pucene\Component\Pucene\Dbal\QueryBuilder\QueryBuilderInterface;
use Pucene\Component\QueryBuilder\Query\QueryInterface;

/**
 * Builds match_all query.
 */
class MatchAllBuilderFactory implements QueryBuilderInterface
{
    public function build(QueryInterface $query, DbalStorage $storage)
    {
        return new MatchAllBuilder();
    }
}
