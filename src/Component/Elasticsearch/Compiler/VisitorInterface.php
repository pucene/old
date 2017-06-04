<?php

namespace Pucene\Component\Elasticsearch\Compiler;

use Pucene\Component\QueryBuilder\Query\QueryInterface;

interface VisitorInterface
{
    /**
     * @param QueryInterface $query
     *
     * @return array
     */
    public function visit(QueryInterface $query);
}
