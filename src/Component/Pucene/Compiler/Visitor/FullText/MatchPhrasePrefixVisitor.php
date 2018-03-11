<?php

namespace Pucene\Component\Pucene\Compiler\Visitor\FullText;

use Pucene\Component\Pucene\Compiler\Element\MatchPhrasePrefixElement;
use Pucene\Component\Pucene\Compiler\ElementInterface;
use Pucene\Component\Pucene\Compiler\VisitorInterface;
use Pucene\Component\Pucene\StorageInterface;
use Pucene\Component\QueryBuilder\Query\FullText\MatchPhrasePrefixQuery;
use Pucene\Component\QueryBuilder\Query\QueryInterface;

class MatchPhrasePrefixVisitor implements VisitorInterface
{
    /**
     * @param MatchPhrasePrefixQuery $query
     */
    public function visit(QueryInterface $query, StorageInterface $storage): ?ElementInterface
    {
        return new MatchPhrasePrefixElement($query->getField(), $query->getPhrase());
    }
}
