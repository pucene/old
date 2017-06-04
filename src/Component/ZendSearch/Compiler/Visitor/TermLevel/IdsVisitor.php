<?php

namespace Pucene\Component\ZendSearch\Compiler\Visitor\TermLevel;

use Pucene\Component\ZendSearch\Compiler\VisitorInterface;
use Pucene\Component\QueryBuilder\Query\QueryInterface;
use Pucene\Component\QueryBuilder\Query\TermLevel\IdsQuery;

class IdsVisitor implements VisitorInterface
{
    /**
     * {@inheritdoc}
     *
     * @param IdsQuery $query
     */
    public function visit(QueryInterface $query)
    {
    }
}
