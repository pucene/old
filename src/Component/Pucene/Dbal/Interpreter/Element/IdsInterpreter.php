<?php

namespace Pucene\Component\Pucene\Dbal\Interpreter\Element;

use Pucene\Component\Math\MathExpressionBuilder;
use Pucene\Component\Pucene\Compiler\Element\IdsElement;
use Pucene\Component\Pucene\Compiler\ElementInterface;
use Pucene\Component\Pucene\Dbal\Interpreter\InterpreterInterface;
use Pucene\Component\Pucene\Dbal\Interpreter\PuceneQueryBuilder;
use Pucene\Component\Pucene\Dbal\ScoringAlgorithm;

class IdsInterpreter implements InterpreterInterface
{
    /**
     * {@inheritdoc}
     *
     * @param IdsElement $element
     */
    public function interpret(ElementInterface $element, PuceneQueryBuilder $queryBuilder)
    {
        $expr = $queryBuilder->expr();

        return $expr->in(
            'document.id',
            array_map(
                function($item) {
                    return "'" . $item . "'";
                },
                $element->getIds()
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function scoring(ElementInterface $element, ScoringAlgorithm $scoring, $queryNorm = null)
    {
        return (new MathExpressionBuilder())->value(1);
    }

    public function newScoring(ElementInterface $element, ScoringAlgorithm $scoring, array $row, $queryNorm = null)
    {
        return 1;
    }

    public function matches(ElementInterface $element, array $row)
    {
        return in_array($row['id'], $element->getIds());
    }
}
