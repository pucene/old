<?php

namespace Pucene\Component\Elasticsearch\QueryBuilder\Query\Specialized;

use Pucene\Component\Elasticsearch\QueryBuilder\QueryBuilderInterface;
use Pucene\Component\QueryBuilder\Query\QueryInterface;

/**
 * Build match query.
 */
class MoreLikeThisBuilder implements QueryBuilderInterface
{
    public function build(QueryInterface $query)
    {
        return new MoreLikeThisQuery($query);
    }
}
