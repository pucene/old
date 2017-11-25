<?php

namespace Pucene\Component\Pucene\Dbal\Math;

use Pucene\Component\Math\ExpressionInterface;
use Pucene\Component\Math\MathExpressionBuilder;

class FieldLengthNorm implements ExpressionInterface
{
    /**
     * @var string
     */
    private $alias;

    /**
     * @var MathExpressionBuilder
     */
    private $expr;

    public function __construct(string $alias, MathExpressionBuilder $expr)
    {
        $this->alias = $alias;
        $this->expr = $expr;
    }

    public function __toString(): string
    {
        return $this->expr->coalesce(
            $this->expr->variable($this->alias . '.field_norm'),
            $this->expr->value(0)
        );
    }
}
