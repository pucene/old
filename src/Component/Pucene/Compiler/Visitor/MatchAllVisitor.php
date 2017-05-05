<?php

namespace Pucene\Component\Pucene\Compiler\Visitor;

use Pucene\Component\Pucene\Compiler\VisitorInterface;
use Pucene\Component\Pucene\StorageInterface;
use Pucene\Component\QueryBuilder\Query\MatchAllQuery;
use Pucene\Component\QueryBuilder\Query\QueryInterface;

class MatchAllVisitor implements VisitorInterface
{
    /**
     * {@inheritdoc}
     *
     * @param MatchAllQuery $query
     */
    public function visit(QueryInterface $query, StorageInterface $storage)
    {
        return null;
    }
}
