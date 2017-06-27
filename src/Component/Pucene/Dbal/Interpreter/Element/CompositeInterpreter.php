<?php

namespace Pucene\Component\Pucene\Dbal\Interpreter\Element;

use Pucene\Component\Pucene\Compiler\Element\BoolElement;
use Pucene\Component\Pucene\Compiler\Element\CompositeElement;
use Pucene\Component\Pucene\Compiler\ElementInterface;
use Pucene\Component\Pucene\Dbal\Interpreter\PuceneQueryBuilder;
use Pucene\Component\Pucene\Dbal\ScoringAlgorithm;

class CompositeInterpreter extends BoolInterpreter
{
    /**
     * {@inheritdoc}
     *
     * @param CompositeElement $element
     */
    public function interpret(ElementInterface $element, PuceneQueryBuilder $queryBuilder)
    {
        if (count($element->getElements()) === 0) {
            return 1;
        } elseif (count($element->getElements()) === 1) {
            $innerElement = $element->getElements()[0];
            $interpreter = $this->interpreterPool->get(get_class($innerElement));

            return $interpreter->interpret($innerElement, $queryBuilder);
        }

        $expr = $queryBuilder->expr();

        $expression = $expr->orX();
        if ($element->getOperator() === CompositeElement:: AND) {
            $expression = $expr->andX();
        }

        // TODO optimization (e.g. or terms can use the same join)

        foreach ($element->getElements() as $innerElement) {
            $interpreter = $this->interpreterPool->get(get_class($innerElement));

            $expression->add($interpreter->interpret($innerElement, $queryBuilder));
        }

        return $expression;
    }

    /**
     * {@inheritdoc}
     *
     * @param CompositeElement $element
     */
    public function scoring(ElementInterface $element, ScoringAlgorithm $scoring, $queryNorm = null)
    {
        return parent::scoring(new BoolElement($element, $element->getElements(), $element->getBoost(), $element->getCoord()), $scoring, $queryNorm);
    }
}
