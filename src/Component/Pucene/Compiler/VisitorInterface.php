<?php

namespace Pucene\Component\Pucene\Compiler;

use Pucene\Component\Pucene\StorageInterface;
use Pucene\Component\QueryBuilder\Query\QueryInterface;

interface VisitorInterface
{
    /**
     * @param QueryInterface $query
     * @param StorageInterface $storage
     *
     * @return ElementInterface
     */
    public function visit(QueryInterface $query, StorageInterface $storage);
}
