<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Math;

use Pucene\Component\Math\ExpressionInterface;
use Pucene\Component\Math\MathExpressionBuilder;
use Pucene\Component\Pucene\Dbal\QueryBuilder\ScoringQueryBuilder;

class FieldLengthNorm implements ExpressionInterface
{
    /**
     * @var
     */
    private $field;

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
     * @param ScoringQueryBuilder $queryBuilder
     * @param MathExpressionBuilder $expr
     */
    public function __construct(string $field, ScoringQueryBuilder $queryBuilder, MathExpressionBuilder $expr)
    {
        $this->field = $field;
        $this->queryBuilder = $queryBuilder;
        $this->expr = $expr;
    }

    public function __toString(): string
    {
        return $this->expr->coalesce(
            $this->expr->variable($this->queryBuilder->joinField($this->field) . '.field_norm'),
            $this->expr->value(0)
        );
    }
}
