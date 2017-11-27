<?php

namespace Pucene\Component\Pucene\Dbal\Interpreter\Element;

use Pucene\Component\Math\MathExpressionBuilder;
use Pucene\Component\Pucene\Compiler\Element\TypeElement;
use Pucene\Component\Pucene\Compiler\ElementInterface;
use Pucene\Component\Pucene\Dbal\Interpreter\InterpreterInterface;
use Pucene\Component\Pucene\Dbal\Interpreter\PuceneQueryBuilder;
use Pucene\Component\Pucene\Dbal\ScoringAlgorithm;

class TypeInterpreter implements InterpreterInterface
{
    /**
     * @param TypeElement $element
     */
    public function interpret(ElementInterface $element, PuceneQueryBuilder $queryBuilder, string $index)
    {
        return $queryBuilder->expr()->eq('document.type', "'" . $element->getType() . "'");
    }

    public function scoring(ElementInterface $element, ScoringAlgorithm $scoring, string $index)
    {
        return (new MathExpressionBuilder())->value(1);
    }
}
