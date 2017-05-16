<?php

namespace Pucene\Component\Pucene\Dbal\Math;

use Pucene\Component\Math\ExpressionInterface;

class IfCondition implements ExpressionInterface
{
    /**
     * @var string
     */
    private $condition;

    /**
     * @var string
     */
    private $trueResult;

    /**
     * @var string
     */
    private $falseResult;

    /**
     * @param string $condition
     * @param string $trueResult
     * @param string $falseResult
     */
    public function __construct(string $condition, string $trueResult, string $falseResult)
    {
        $this->condition = $condition;
        $this->trueResult = $trueResult;
        $this->falseResult = $falseResult;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return sprintf('(IF(%s, %s, %s))', $this->condition, $this->trueResult, $this->falseResult);
    }
}
