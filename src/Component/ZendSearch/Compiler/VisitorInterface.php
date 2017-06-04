<?php

namespace Pucene\Component\ZendSearch\Compiler;

use Pucene\Component\QueryBuilder\Query\QueryInterface;
use ZendSearch\Lucene\Search\Query\AbstractQuery;

interface VisitorInterface
{
    /**
     * @param QueryInterface $query
     *
     * @return AbstractQuery
     */
    public function visit(QueryInterface $query);
}
