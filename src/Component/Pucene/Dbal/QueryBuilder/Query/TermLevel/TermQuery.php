<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Query\TermLevel;

use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Pucene\Component\Math\MathExpressionBuilder;
use Pucene\Component\Pucene\Dbal\QueryBuilder\Math\FieldLengthNorm;
use Pucene\Component\Pucene\Dbal\QueryBuilder\Math\TermFrequency;
use Pucene\Component\Pucene\Dbal\QueryBuilder\ParameterBag;
use Pucene\Component\Pucene\Dbal\QueryBuilder\QueryInterface;
use Pucene\Component\Pucene\Dbal\QueryBuilder\ScoringQueryBuilder;
use Pucene\Component\QueryBuilder\Query\TermLevel\Term;

/**
 * Represents term query.
 */
class TermQuery implements QueryInterface
{
    /**
     * @var Term
     */
    private $query;

    /**
     * @param Term $query
     */
    public function __construct(Term $query)
    {
        $this->query = $query;
    }

    public function getField()
    {
        return $this->query->getField();
    }

    public function getTerm()
    {
        return $this->query->getTerm();
    }

    public function build(ExpressionBuilder $expr, ParameterBag $parameter)
    {
        $parameter->add($this->query->getField());
        $parameter->add($this->query->getTerm());

        return $expr->andX(
            $expr->eq('field.name', '?'),
            $expr->eq('token.term', '?')
        );
    }

    public function scoring(MathExpressionBuilder $expr, ScoringQueryBuilder $queryBuilder)
    {
        return $expr->multiply(
            new TermFrequency($this->getField(), $this->getTerm(), $queryBuilder, $expr),
            $expr->value($queryBuilder->inverseDocumentFrequency($this->getTerm() . '.id')),
            new FieldLengthNorm($this->getField(), $queryBuilder, $expr, $expr)
        );
    }
}
