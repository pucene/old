<?php

namespace Pucene\Component\Pucene\Compiler\Visitor\TermLevel;

use Pucene\Component\Pucene\Compiler\Element\CompositeElement;
use Pucene\Component\Pucene\Compiler\Element\IdsElement;
use Pucene\Component\Pucene\Compiler\Element\TypeElement;
use Pucene\Component\Pucene\Compiler\VisitorInterface;
use Pucene\Component\Pucene\StorageInterface;
use Pucene\Component\QueryBuilder\Query\QueryInterface;
use Pucene\Component\QueryBuilder\Query\TermLevel\IdsQuery;

class IdsVisitor implements VisitorInterface
{
    /**
     * {@inheritdoc}
     *
     * @param IdsQuery $query
     */
    public function visit(QueryInterface $query, StorageInterface $storage)
    {
        $ids = new IdsElement($query->getValues());
        if (!$query->getType()) {
            return $ids;
        }

        return new CompositeElement(CompositeElement:: OPERATOR_AND, [$ids, new TypeElement($query->getType())]);
    }
}
