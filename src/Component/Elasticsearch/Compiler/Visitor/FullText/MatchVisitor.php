<?php

namespace Pucene\Component\Elasticsearch\Compiler\Visitor\FullText;

use Pucene\Component\Elasticsearch\Compiler\VisitorInterface;
use Pucene\Component\QueryBuilder\Query\FullText\MatchQuery;
use Pucene\Component\QueryBuilder\Query\QueryInterface;

class MatchVisitor implements VisitorInterface
{
    /**
     * {@inheritdoc}
     *
     * @param MatchQuery $query
     */
    public function visit(QueryInterface $query)
    {
        return ['match' => [$query->getField() => $query->getQuery()]];
    }
}
