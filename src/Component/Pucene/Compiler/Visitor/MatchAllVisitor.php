<?php

namespace Pucene\Component\Pucene\Compiler\Visitor;

use Pucene\Component\Pucene\Compiler\Element\MatchAllElement;
use Pucene\Component\Pucene\Compiler\ElementInterface;
use Pucene\Component\Pucene\Compiler\VisitorInterface;
use Pucene\Component\Pucene\StorageInterface;
use Pucene\Component\QueryBuilder\Query\MatchAllQuery;
use Pucene\Component\QueryBuilder\Query\QueryInterface;

class MatchAllVisitor implements VisitorInterface
{
    /**
     * @param MatchAllQuery $query
     */
    public function visit(QueryInterface $query, StorageInterface $storage): ?ElementInterface
    {
        return new MatchAllElement();
    }
}
