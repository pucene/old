<?php

namespace Pucene\Component\Elasticsearch\Compiler\Visitor\TermLevel;

use Pucene\Component\Elasticsearch\Compiler\VisitorInterface;
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
        return ['ids' => ['values' => $query->getValues(), 'type' => $query->getType()]];
    }
}
