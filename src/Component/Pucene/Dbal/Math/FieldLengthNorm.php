<?php

namespace Pucene\Component\Pucene\Dbal\Math;

use Pucene\Component\Math\ExpressionInterface;
use Pucene\Component\Math\MathExpressionBuilder;
use Pucene\Component\Pucene\Dbal\Interpreter\PuceneQueryBuilder;
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
     * @param PuceneQueryBuilder $queryBuilder
     */
    public function __construct(string $field, PuceneQueryBuilder $queryBuilder)
    {
        $this->field = $field;
        $this->queryBuilder = $queryBuilder;
        $this->expr = $queryBuilder->math();
    }

    public function __toString(): string
    {
        return $this->expr->coalesce(
            $this->expr->variable($this->queryBuilder->joinField($this->field) . '.field_norm'),
            $this->expr->value(0)
        );
    }
}
