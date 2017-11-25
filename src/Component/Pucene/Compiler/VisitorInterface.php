<?php

namespace Pucene\Component\Pucene\Compiler;

use Pucene\Component\Pucene\StorageInterface;
use Pucene\Component\QueryBuilder\Query\QueryInterface;

interface VisitorInterface
{
    public function visit(QueryInterface $query, StorageInterface $storage): ?ElementInterface;
}
