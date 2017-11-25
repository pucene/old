<?php

namespace Pucene\Component\Elasticsearch\Compiler\Visitor\TermLevel;

use Pucene\Component\Elasticsearch\Compiler\VisitorInterface;
use Pucene\Component\QueryBuilder\Query\QueryInterface;
use Pucene\Component\QueryBuilder\Query\TermLevel\TermQuery;

class TermVisitor implements VisitorInterface
{
    /**
     * @param TermQuery $query
     */
    public function visit(QueryInterface $query): array
    {
        return ['term' => [$query->getField() => $query->getTerm()]];
    }
}
