<?php

namespace Pucene\Component\Pucene\Dbal\Interpreter\Element;

use Pucene\Component\Pucene\Compiler\Element\TermElement;
use Pucene\Component\Pucene\Compiler\ElementInterface;
use Pucene\Component\Pucene\Dbal\Interpreter\Fuzzy;
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

        if (!$element->getFuzzy()) {
            return $expr->isNotNull(
                $queryBuilder->joinTerm($element->getField(), $element->getTerm()) . '.id'
            );
        }

        $terms = Fuzzy::getFuzzyTerms($element->getTerm(), $element->getFuzzy());

        $termName = $queryBuilder->joinTermFuzzy($element->getField(), $element->getTerm());
        $format = $termName . '.term LIKE \'%s\'';
        $parts = [];
        foreach ($terms as $term) {
            $parts[] = sprintf($format, $term);
        }

        if ($element->getFuzzy() === 'auto') {
            return implode(' OR ', $parts);
        }

        return sprintf(
            '(LENGTH(%1$s.term) - %2$s) BETWEEN -%3$s AND %3$s AND (%4$s)',
            $termName,
            strlen($element->getTerm()),
            $element->getFuzzy(),
            implode(' OR ', $parts)
        );
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
