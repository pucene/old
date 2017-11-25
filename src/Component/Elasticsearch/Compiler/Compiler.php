<?php

namespace Pucene\Component\Elasticsearch\Compiler;

use Pucene\Component\QueryBuilder\Query\QueryInterface;
use Pucene\Component\Symfony\Pool\PoolInterface;

class Compiler
{
    /**
     * @var PoolInterface
     */
    private $visitors;

    /**
     * @param PoolInterface $visitors
     */
    public function __construct(PoolInterface $visitors)
    {
        $this->visitors = $visitors;
    }

    public function compile(QueryInterface $query): array
    {
        return $this->getVisitor($query)->visit($query);
    }

    private function getVisitor(QueryInterface $query): VisitorInterface
    {
        return $this->visitors->get(get_class($query));
    }
}
