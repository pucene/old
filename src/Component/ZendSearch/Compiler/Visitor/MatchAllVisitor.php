<?php

namespace Pucene\Component\ZendSearch\Compiler\Visitor;

use Pucene\Component\ZendSearch\Compiler\VisitorInterface;
use Pucene\Component\QueryBuilder\Query\MatchAllQuery;
use Pucene\Component\QueryBuilder\Query\QueryInterface;

class MatchAllVisitor implements VisitorInterface
{
    /**
     * {@inheritdoc}
     *
     * @param MatchAllQuery $query
     */
    public function visit(QueryInterface $query)
    {
    }
}
