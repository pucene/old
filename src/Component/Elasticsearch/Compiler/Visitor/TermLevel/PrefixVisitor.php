<?php

namespace Pucene\Component\Elasticsearch\Compiler\Visitor\TermLevel;

use Pucene\Component\Elasticsearch\Compiler\VisitorInterface;
use Pucene\Component\QueryBuilder\Query\QueryInterface;
use Pucene\Component\QueryBuilder\Query\TermLevel\PrefixQuery;

class PrefixVisitor implements VisitorInterface
{
    /**
     * @param PrefixQuery $query
     */
    public function visit(QueryInterface $query): array
    {
        return [PrefixQuery::NAME => [$query->getField() => $query->getPrefix()]];
    }
}
