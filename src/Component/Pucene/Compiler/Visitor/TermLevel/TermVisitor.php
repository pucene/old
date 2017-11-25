<?php

namespace Pucene\Component\Pucene\Compiler\Visitor\TermLevel;

use Pucene\Component\Pucene\Compiler\Element\TermElement;
use Pucene\Component\Pucene\Compiler\ElementInterface;
use Pucene\Component\Pucene\Compiler\VisitorInterface;
use Pucene\Component\Pucene\StorageInterface;
use Pucene\Component\QueryBuilder\Query\QueryInterface;
use Pucene\Component\QueryBuilder\Query\TermLevel\TermQuery;

class TermVisitor implements VisitorInterface
{
    /**
     * @param TermQuery $query
     */
    public function visit(QueryInterface $query, StorageInterface $storage): ?ElementInterface
    {
        return new TermElement($query->getField(), $query->getTerm());
    }
}
