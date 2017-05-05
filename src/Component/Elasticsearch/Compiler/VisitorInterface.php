<?php

namespace Pucene\Component\Elasticsearch\Compiler;

use Pucene\Component\QueryBuilder\Query\QueryInterface;

interface VisitorInterface
{
    /**
     * @param QueryInterface $query
     *
     * @return ElementInterface
     */
    public function visit(QueryInterface $query);
}
