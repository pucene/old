<?php

namespace Pucene\Component\Elasticsearch\Compiler\Visitor\TermLevel;

use Pucene\Component\Elasticsearch\Compiler\VisitorInterface;
use Pucene\Component\QueryBuilder\Query\QueryInterface;
use Pucene\Component\QueryBuilder\Query\TermLevel\RangeQuery;

class RangeVisitor implements VisitorInterface
{
    /**
     * @param RangeQuery $query
     */
    public function visit(QueryInterface $query): array
    {
        return [
            'range' => [
                $query->getField() => array_filter(
                    [
                        'gte' => $query->getGte(),
                        'gt' => $query->getGt(),
                        'lte' => $query->getLte(),
                        'lt' => $query->getLt(),
                    ]
                ),
            ],
        ];
    }
}
