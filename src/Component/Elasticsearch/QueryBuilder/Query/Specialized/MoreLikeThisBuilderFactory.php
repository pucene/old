<?php

namespace Pucene\Component\Elasticsearch\QueryBuilder\Query\Specialized;

use Pucene\Component\Elasticsearch\QueryBuilder\QueryBuilderInterface;
use Pucene\Component\QueryBuilder\Query\QueryInterface;

/**
 * Build match query.
 */
class MoreLikeThisBuilderFactory implements QueryBuilderInterface
{
    public function build(QueryInterface $query)
    {
        return new MoreLikeThisBuilder($query);
    }
}
