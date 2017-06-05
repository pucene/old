<?php

namespace Pucene\Component\Pucene\Dbal\Interpreter\Element;

use Pucene\Component\Math\MathExpressionBuilder;
use Pucene\Component\Pucene\Compiler\Element\TermElement;
use Pucene\Component\Pucene\Compiler\ElementInterface;
use Pucene\Component\Pucene\Dbal\Interpreter\InterpreterInterface;
use Pucene\Component\Pucene\Dbal\Interpreter\PuceneQueryBuilder;
use Pucene\Component\Pucene\Dbal\ScoringAlgorithm;

class MatchAllInterpreter implements InterpreterInterface
{
    /**
     * {@inheritdoc}
     *
     * @param TermElement $element
     */
    public function interpret(ElementInterface $element, PuceneQueryBuilder $queryBuilder)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @param TermElement $element
     */
    public function scoring(ElementInterface $element, ScoringAlgorithm $scoring, $queryNorm = null)
    {
        return (new MathExpressionBuilder())->value(1);
    }

    public function newScoring(ElementInterface $element, ScoringAlgorithm $scoring, array $row, $queryNorm = null)
    {
        return 1;
    }
}
