<?php

namespace Pucene\Component\ZendSearch\Compiler\Visitor;

use Pucene\Component\QueryBuilder\Query\FullText\MatchQuery;
use Pucene\Component\QueryBuilder\Query\MatchAllQuery;
use Pucene\Component\QueryBuilder\Query\TermLevel\TermQuery;
use Pucene\Component\Symfony\Pool\PoolInterface;
use Pucene\Component\ZendSearch\Compiler\Visitor\FullText\MatchVisitor;
use Pucene\Component\ZendSearch\Compiler\Visitor\TermLevel\TermVisitor;

class VisitorPool implements PoolInterface
{
    private $visitors = [];

    public function __construct()
    {
        $this->visitors = [
            TermQuery::class => new TermVisitor(),
            MatchAllQuery::class => new MatchAllVisitor(),
            MatchQuery::class => new MatchVisitor(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function get($alias)
    {
        return $this->visitors[$alias];
    }
}
