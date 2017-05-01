<?php

namespace Pucene\Component\Math\Expression;

use Pucene\Component\Math\ExpressionInterface;

class Variable implements ExpressionInterface
{
    /**
     * @var string
     */
    private $variable;

    /**
     * @param string $variable
     */
    public function __construct($variable)
    {
        $this->variable = $variable;
    }

    public function __toString(): string
    {
        return '(' . $this->variable . ')';
    }
}
