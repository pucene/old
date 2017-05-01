<?php

namespace Pucene\Component\Math;

use Pucene\Component\Math\Expression\CompositeExpression;
use Pucene\Component\Math\Expression\FunctionExpression;
use Pucene\Component\Math\Expression\Value;
use Pucene\Component\Math\Expression\Variable;

class MathExpressionBuilder
{
    public function variable(string $x): ExpressionInterface
    {
        return new Variable($x);
    }

    public function value(float $x): ExpressionInterface
    {
        return new Value($x);
    }

    public function add(...$x): CompositeExpressionInterface
    {
        return new CompositeExpression($x, '+');
    }

    public function substract(...$x): CompositeExpressionInterface
    {
        return new CompositeExpression($x, '-');
    }

    public function multiply(...$x): CompositeExpressionInterface
    {
        return new CompositeExpression($x, '*');
    }

    public function devide(...$x): CompositeExpressionInterface
    {
        return new CompositeExpression($x, '/');
    }

    public function count(ExpressionInterface $x): ExpressionInterface
    {
        return new FunctionExpression('count', [$x]);
    }

    public function coalesce(ExpressionInterface ...$x): ExpressionInterface
    {
        return new FunctionExpression('coalesce', $x);
    }

    public function sqrt($x)
    {
        return new FunctionExpression('sqrt', [$x]);
    }
}
