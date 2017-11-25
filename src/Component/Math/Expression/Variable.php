<?php

namespace Pucene\Component\Math\Expression;

use Pucene\Component\Math\ExpressionInterface;

class Variable implements ExpressionInterface
{
    /**
     * @var string
     */
    private $variable;

    public function __construct(string $variable)
    {
        $this->variable = $variable;
    }

    public function __toString(): string
    {
        return '(' . $this->variable . ')';
    }
}
