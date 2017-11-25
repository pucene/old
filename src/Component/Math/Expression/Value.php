<?php

namespace Pucene\Component\Math\Expression;

use Pucene\Component\Math\ExpressionInterface;

class Value implements ExpressionInterface
{
    /**
     * @var float
     */
    private $value;

    public function __construct(float $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
