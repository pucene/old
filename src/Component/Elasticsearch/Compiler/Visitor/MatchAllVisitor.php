<?php

namespace Pucene\Component\Elasticsearch\Compiler\Visitor;

use Pucene\Component\Elasticsearch\Compiler\VisitorInterface;
use Pucene\Component\QueryBuilder\Query\MatchAllQuery;
use Pucene\Component\QueryBuilder\Query\QueryInterface;

class MatchAllVisitor implements VisitorInterface
{
    /**
     * @param MatchAllQuery $query
     */
    public function visit(QueryInterface $query): array
    {
        return ['match_all' => new \stdClass()];
    }
}
