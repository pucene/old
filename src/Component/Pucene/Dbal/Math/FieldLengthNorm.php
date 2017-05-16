<?php

namespace Pucene\Component\Pucene\Dbal\Math;

use Pucene\Component\Math\ExpressionInterface;
use Pucene\Component\Math\MathExpressionBuilder;
use Pucene\Component\Pucene\Dbal\Interpreter\PuceneQueryBuilder;

class FieldLengthNorm implements ExpressionInterface
{
    /**
     * @var
     */
    private $field;

    /**
     * @var string
     */
    private $term;

    /**
     * @var PuceneQueryBuilder
     */
    private $queryBuilder;

    /**
     * @var MathExpressionBuilder
     */
    private $expr;

    /**
     * @param string $field
     * @param string $term
     * @param PuceneQueryBuilder $queryBuilder
     */
    public function __construct(string $field, string $term, PuceneQueryBuilder $queryBuilder)
    {
        $this->field = $field;
        $this->term = $term;
        $this->queryBuilder = $queryBuilder;
        $this->expr = $queryBuilder->math();
    }

    public function __toString(): string
    {
        return $this->expr->coalesce(
            $this->expr->variable($this->queryBuilder->joinTerm($this->field, $this->term) . '.field_norm'),
            $this->expr->value(0)
        );
    }
}
