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
        $name = $queryBuilder->joinTerm($element->getField(), $element->getTerm());

        $queryBuilder->addSelect(sprintf('(%1$s.score) as %1$sValue', $name));
        $queryBuilder->addSelect(sprintf('%1$s.idf as %1$sIdf', $name));

        return $expr->isNotNull($name . '.id');
    }

    /**
     * {@inheritdoc}
     *
     * @param TermElement $element
     */
    public function scoring(ElementInterface $element, ScoringAlgorithm $scoring, $queryNorm = null)
    {
        return $scoring->scoreTerm($element, $queryNorm, $element->getBoost());
    }

    /**
     * {@inheritdoc}
     *
     * @param TermElement $element
     */
    public function newScoring(ElementInterface $element, ScoringAlgorithm $scoring, array $row, $queryNorm = null)
    {
        $termName = 'term' . ucfirst($element->getField()) . ucfirst($element->getTerm());
        $idfName = $termName . 'Idf';
        $valueName = $termName . 'Value';
        if (!array_key_exists($valueName, $row)
            || !array_key_exists($idfName, $row)
            || $row[$valueName] === null
            || $row[$idfName] === null
        ) {
            return 0;
        }

        $idf = $row[$idfName];
        $factor = $idf * $element->getBoost();
        if ($queryNorm) {
            $factor *= $idf * $queryNorm;
        }

        return $row[$valueName] * $factor;
    }

    /**
     * {@inheritdoc}
     *
     * @param TermElement $element
     */
    public function matches(ElementInterface $element, array $row)
    {
        $termName = 'term' . ucfirst($element->getField()) . ucfirst($element->getTerm()) . 'Value';

        return array_key_exists($termName, $row) && $row[$termName] !== null;
    }
}
