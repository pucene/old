<?php

namespace Pucene\Component\Math\Expression;

use Pucene\Component\Math\ExpressionInterface;

class FunctionExpression implements ExpressionInterface
{
    /**
     * @var string
     */
    private $function;

    /**
     * @var ExpressionInterface[]
     */
    private $parameter;

    public function __construct(string $function, array $parameter)
    {
        $this->parameter = $parameter;
        $this->function = $function;
    }

    public function __toString(): string
    {
        return sprintf(
            '%s(%s)',
            $this->function,
            implode(
                ', ',
                array_map(
                    function (ExpressionInterface $expression) {
                        return $expression->__toString();
                    },
                    $this->parameter
                )
            )
        );
    }
}
