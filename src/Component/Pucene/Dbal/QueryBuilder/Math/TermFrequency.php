<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Math;

use Pucene\Component\Math\ExpressionInterface;
use Pucene\Component\Math\MathExpressionBuilder;
use Pucene\Component\Pucene\Dbal\QueryBuilder\ScoringQueryBuilder;

class TermFrequency implements ExpressionInterface
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
     * @var ScoringQueryBuilder
     */
    private $queryBuilder;

    /**
     * @var MathExpressionBuilder
     */
    private $expr;

    /**
     * @param string $field
     * @param string $term
     * @param ScoringQueryBuilder $queryBuilder
     * @param MathExpressionBuilder $expr
     */
    public function __construct(
        string $field,
        string $term,
        ScoringQueryBuilder $queryBuilder,
        MathExpressionBuilder $expr
    ) {
        $this->field = $field;
        $this->term = $term;
        $this->queryBuilder = $queryBuilder;
        $this->expr = $expr;
    }

    public function __toString(): string
    {
        return $this->expr->coalesce(
            $this->expr->sqrt(
                $this->expr->variable($this->queryBuilder->joinTerm($this->field, $this->term) . '.frequency')
            ),
            $this->expr->value(0)
        );
    }
}
