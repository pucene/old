<?php

namespace Pucene\Component\Elasticsearch\QueryBuilder\Query\TermLevel;

use Pucene\Component\Elasticsearch\QueryBuilder\QueryBuilderInterface;
use Pucene\Component\QueryBuilder\Query\QueryInterface;

class IdsBuilderFactory implements QueryBuilderInterface
{
    public function build(QueryInterface $query)
    {
        return new IdsBuilder($query);
    }
}
