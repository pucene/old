<?php

namespace Pucene\Component\Elasticsearch\Compiler;

use Pucene\Component\QueryBuilder\Query\QueryInterface;

interface VisitorInterface
{
    public function visit(QueryInterface $query): array;
}
