<?php

namespace Pucene\Component\Pucene\Dbal\Interpreter\Element;

use Pucene\Component\Pucene\Compiler\Element\TermElement;
use Pucene\Component\Pucene\Compiler\ElementInterface;
use Pucene\Component\Pucene\Dbal\Interpreter\InterpreterInterface;
use Pucene\Component\Pucene\Dbal\Interpreter\PuceneQueryBuilder;
use Pucene\Component\Pucene\Dbal\ScoringAlgorithm;

class TermInterpreter implements InterpreterInterface
{
    /**
     * {@inheritdoc}
     *
     * @param TermElement $element
     */
    public function interpret(ElementInterface $element, PuceneQueryBuilder $queryBuilder)
    {
        $expr = $queryBuilder->expr();

        return $expr->isNotNull($queryBuilder->joinTerm($element->getField(), $element->getTerm()) . '.id');
    }

    /**
     * {@inheritdoc}
     *
     * @param TermElement $element
     */
    public function scoring(ElementInterface $element, ScoringAlgorithm $scoring, $queryNorm = null)
    {
        return $scoring->scoreTerm($element, $queryNorm);
    }
}
