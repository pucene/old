<?php

namespace Pucene\Component\Pucene\Compiler\Visitor\Compound;

use Pucene\Component\Pucene\Compiler\Element\BoolElement;
use Pucene\Component\Pucene\Compiler\Element\CompositeElement;
use Pucene\Component\Pucene\Compiler\Element\NotElement;
use Pucene\Component\Pucene\Compiler\ElementInterface;
use Pucene\Component\Pucene\Compiler\VisitorInterface;
use Pucene\Component\Pucene\StorageInterface;
use Pucene\Component\QueryBuilder\Query\Compound\BoolQuery;
use Pucene\Component\QueryBuilder\Query\QueryInterface;
use Pucene\Component\Symfony\Pool\PoolInterface;

class BoolVisitor implements VisitorInterface
{
    /**
     * @var PoolInterface
     */
    private $interpreterPool;

    /**
     * @param PoolInterface $interpreterPool
     */
    public function __construct(PoolInterface $interpreterPool)
    {
        $this->interpreterPool = $interpreterPool;
    }

    /**
     * {@inheritdoc}
     *
     * @param BoolQuery $query
     */
    public function visit(QueryInterface $query, StorageInterface $storage)
    {
        $shouldElements = $this->getElements($query->getShouldQueries(), $storage);
        $mustElements = $this->getElements($query->getMustQueries(), $storage);
        $mustNotElements = $this->getElements($query->getMustNotQueries(), $storage);
        $filterElements = $this->getElements($query->getFilterQueries(), $storage);

        $andElements = array_merge($filterElements, $mustElements);
        foreach ($mustNotElements as $element) {
            $andElements[] = new NotElement($element);
        }

        if (count($andElements) === 0) {
            return new CompositeElement(CompositeElement::OPERATOR_OR, $shouldElements);
        }

        return new BoolElement(
            new CompositeElement(CompositeElement::OPERATOR_AND, $andElements),
            array_merge($mustElements, $shouldElements)
        );
    }

    /**
     * Interprets given queries into elements.
     *
     * @param QueryInterface[] $queries
     * @param StorageInterface $storage
     *
     * @return ElementInterface[]
     */
    private function getElements(array $queries, StorageInterface $storage)
    {
        $elements = [];
        foreach ($queries as $query) {
            $elements[] = $this->getInterpreter($query)->visit($query, $storage);
        }

        return $elements;
    }

    /**
     * @param QueryInterface $query
     *
     * @return VisitorInterface
     */
    private function getInterpreter(QueryInterface $query)
    {
        return $this->interpreterPool->get(get_class($query));
    }
}
