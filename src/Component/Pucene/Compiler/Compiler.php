<?php

namespace Pucene\Component\Pucene\Compiler;

use Pucene\Component\Pucene\StorageInterface;
use Pucene\Component\QueryBuilder\Query\QueryInterface;
use Pucene\Component\Symfony\Pool\PoolInterface;

class Compiler
{
    /**
     * @var PoolInterface
     */
    private $visitors;

    public function __construct(PoolInterface $visitors)
    {
        $this->visitors = $visitors;
    }

    public function compile(QueryInterface $query, StorageInterface $storage): ElementInterface
    {
        return $this->getVisitor($query)->visit($query, $storage);
    }

    private function getVisitor(QueryInterface $query): VisitorInterface
    {
        return $this->visitors->get(get_class($query));
    }
}
