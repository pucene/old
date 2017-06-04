<?php

namespace Pucene\Component\ZendSearch\Compiler\Visitor\TermLevel;

use Pucene\Component\QueryBuilder\Query\QueryInterface;
use Pucene\Component\QueryBuilder\Query\TermLevel\TermQuery;
use Pucene\Component\ZendSearch\Compiler\VisitorInterface;
use ZendSearch\Lucene\Search\Query\Term;
use ZendSearch\Lucene\Index;

class TermVisitor implements VisitorInterface
{
    /**
     * {@inheritdoc}
     *
     * @param TermQuery $query
     */
    public function visit(QueryInterface $query)
    {
        return new Term(new Index\Term($query->getTerm(), $query->getField()));
    }
}
