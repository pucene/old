<?php

namespace Pucene\Component\ZendSearch\Compiler\Visitor\Compound;

use Pucene\Component\ZendSearch\Compiler\VisitorInterface;
use Pucene\Component\QueryBuilder\Query\Compound\BoolQuery;
use Pucene\Component\QueryBuilder\Query\QueryInterface;
use Pucene\Component\Symfony\Pool\PoolInterface;

class BoolVisitor implements VisitorInterface
{
    /**
     * @var PoolInterface
     */
    private $interpreterPool;

    /**
     * @param PoolInterface $interpreterPool
     */
    public function __construct(PoolInterface $interpreterPool)
    {
        $this->interpreterPool = $interpreterPool;
    }

    /**
     * {@inheritdoc}
     *
     * @param BoolQuery $query
     */
    public function visit(QueryInterface $query)
    {
    }
}
