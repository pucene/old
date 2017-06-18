<?php

namespace Pucene\Component\Pucene\Dbal\Math;

use Pucene\Component\Math\ExpressionInterface;
use Pucene\Component\Math\MathExpressionBuilder;

class TermFrequency implements ExpressionInterface
{
    /**
     * @var string
     */
    private $alias;

    /**
     * @var MathExpressionBuilder
     */
    private $expr;

    /**
     * @param string $alias
     * @param MathExpressionBuilder $expr
     */
    public function __construct(string $alias, MathExpressionBuilder $expr)
    {
        $this->alias = $alias;
        $this->expr = $expr;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return $this->expr->coalesce(
            $this->expr->sqrt($this->expr->variable($this->alias . '.term_frequency')),
            $this->expr->value(0)
        );
    }
}
