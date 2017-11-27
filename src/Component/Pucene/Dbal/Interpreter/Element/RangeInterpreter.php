<?php

namespace Pucene\Component\Pucene\Dbal\Interpreter\Element;

use Pucene\Component\Mapping\Types;
use Pucene\Component\Pucene\Compiler\Element\BoolElement;
use Pucene\Component\Pucene\Compiler\Element\RangeElement;
use Pucene\Component\Pucene\Compiler\ElementInterface;
use Pucene\Component\Pucene\Dbal\Interpreter\InterpreterInterface;
use Pucene\Component\Pucene\Dbal\Interpreter\PuceneQueryBuilder;
use Pucene\Component\Pucene\Dbal\ScoringAlgorithm;
use Pucene\Component\Pucene\Mapping\Mapping;

class RangeInterpreter implements InterpreterInterface
{
    /**
     * @var Mapping
     */
    private $mapping;

    public function __construct(Mapping $mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * @param RangeElement $element
     */
    public function interpret(ElementInterface $element, PuceneQueryBuilder $queryBuilder, string $index)
    {
        $expr = $queryBuilder->expr();
        $type = $this->mapping->getTypeForField($index, $element->getField());
        $alias = $queryBuilder->joinValue($element->getField(), $type);
        $value = $element->getValue();

        if (Types::DATE === $type) {
            $date = new \DateTime($value);
            $value = $date ? $date->format('Y-m-d H:i:s') : null;
        } elseif (Types::BOOLEAN === $type) {
            $value = $value ? 1 : 0;
        }

        return $expr->{$element->getOperator()}($alias . '.value', "'" . $value . "'");
    }

    /**
     * @param BoolElement $element
     */
    public function scoring(ElementInterface $element, ScoringAlgorithm $scoring, string $index)
    {
        return $scoring->getQueryBuilder()->math()->value(1);
    }
}
