<?php

namespace Pucene\Component\Pucene\Compiler\Visitor\TermLevel;

use Pucene\Component\Pucene\Compiler\Element\CompositeElement;
use Pucene\Component\Pucene\Compiler\Element\RangeElement;
use Pucene\Component\Pucene\Compiler\ElementInterface;
use Pucene\Component\Pucene\Compiler\VisitorInterface;
use Pucene\Component\Pucene\StorageInterface;
use Pucene\Component\QueryBuilder\Query\QueryInterface;
use Pucene\Component\QueryBuilder\Query\TermLevel\RangeQuery;

class RangeVisitor implements VisitorInterface
{
    /**
     * @param RangeQuery $query
     */
    public function visit(QueryInterface $query, StorageInterface $storage): ?ElementInterface
    {
        $elements = [];
        if ($query->getGte()) {
            $elements[] = new RangeElement($query->getField(), RangeElement::OPERATOR_GTE, $query->getGte());
        }
        if ($query->getGt()) {
            $elements[] = new RangeElement($query->getField(), RangeElement::OPERATOR_GT, $query->getGt());
        }
        if ($query->getLte()) {
            $elements[] = new RangeElement($query->getField(), RangeElement::OPERATOR_LTE, $query->getLte());
        }
        if ($query->getLt()) {
            $elements[] = new RangeElement($query->getField(), RangeElement::OPERATOR_LT, $query->getLt());
        }

        if (1 === count($elements)) {
            return reset($elements);
        }

        return new CompositeElement(CompositeElement::AND, $elements);
    }
}
