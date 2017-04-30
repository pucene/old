<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Query\TermLevel;

use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Pucene\Component\Math\MathExpressionBuilder;
use Pucene\Component\Pucene\Dbal\QueryBuilder\Math\FieldLengthNorm;
use Pucene\Component\Pucene\Dbal\QueryBuilder\Math\TermFrequency;
use Pucene\Component\Pucene\Dbal\QueryBuilder\ParameterBag;
use Pucene\Component\Pucene\Dbal\QueryBuilder\QueryInterface;
use Pucene\Component\Pucene\Dbal\QueryBuilder\ScoringQueryBuilder;

/**
 * Represents term query.
 */
class TermBuilder implements QueryInterface
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $term;

    /**
     * @var float
     */
    private $boost;

    /**
     * @param string $field
     * @param string $term
     * @param float $boost
     */
    public function __construct(string $field, string $term, float $boost = 1)
    {
        $this->field = $field;
        $this->term = $term;
        $this->boost = $boost;
    }

    public function getField()
    {
        return $this->field;
    }

    public function getTerm()
    {
        return $this->term;
    }

    public function build(ExpressionBuilder $expr, ParameterBag $parameter)
    {
        return $expr->andX(
            $expr->eq('field.name', "'" . $this->field . "'"),
            $expr->eq('token.term', "'" . $this->term . "'")
        );
    }

    public function scoring(MathExpressionBuilder $expr, ScoringQueryBuilder $queryBuilder, $queryNorm = null)
    {
        $inverseDocumentFrequency = $queryBuilder->inverseDocumentFrequency($this);

        $expression = $expr->multiply(
            new TermFrequency($this->getField(), $this->getTerm(), $queryBuilder, $expr),
            $expr->value($inverseDocumentFrequency),
            new FieldLengthNorm($this->getField(), $queryBuilder, $expr),
            $this->boost
        );

        if ($queryNorm) {
            $expression->add($expr->value($queryNorm * $inverseDocumentFrequency));
        }

        return $expression;
    }

    public function getTerms()
    {
        return [$this];
    }
}
