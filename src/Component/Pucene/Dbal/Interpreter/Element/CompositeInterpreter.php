<?php

namespace Pucene\Component\Pucene\Dbal\Interpreter\Element;

use Pucene\Component\Pucene\Compiler\Element\BoolElement;
use Pucene\Component\Pucene\Compiler\Element\CompositeElement;
use Pucene\Component\Pucene\Compiler\ElementInterface;
use Pucene\Component\Pucene\Dbal\Interpreter\InterpreterInterface;
use Pucene\Component\Pucene\Dbal\Interpreter\PuceneQueryBuilder;
use Pucene\Component\Pucene\Dbal\ScoringAlgorithm;

class CompositeInterpreter extends BoolInterpreter
{
    /**
     * @param CompositeElement $element
     */
    public function interpret(ElementInterface $element, PuceneQueryBuilder $queryBuilder, string $index)
    {
        $expr = $queryBuilder->expr();
        if (0 === count($element->getElements())) {
            return 1;
        } elseif (1 === count($element->getElements())) {
            $innerElement = $element->getElements()[0];
            /** @var InterpreterInterface $interpreter */
            $interpreter = $this->interpreterPool->get(get_class($innerElement));

            return $interpreter->interpret($innerElement, $queryBuilder, $index);
        }

        $expression = $expr->orX();
        if (CompositeElement:: AND === $element->getOperator()) {
            $expression = $expr->andX();
        }

        // TODO optimization (e.g. or terms can use the same join)

        foreach ($element->getElements() as $innerElement) {
            $expression->add($this->getInterpreter($innerElement)->interpret($innerElement, $queryBuilder, $index));
        }

        return $expression;
    }

    /**
     * @param CompositeElement $element
     */
    public function scoring(ElementInterface $element, ScoringAlgorithm $scoring, string $index)
    {
        if (!$element->isScoring()) {
            return $scoring->getQueryBuilder()->math()->value(1);
        }

        return parent::scoring(
            new BoolElement($element, $element->getElements(), $element->getBoost()),
            $scoring,
            $index
        );
    }

    private function getInterpreter(ElementInterface $element): InterpreterInterface
    {
        return $this->interpreterPool->get(get_class($element));
    }
}
