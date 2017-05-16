<?php

namespace Pucene\Component\Pucene\Dbal\Math;

use Pucene\Component\Math\Expression\Value;
use Pucene\Component\Math\ExpressionInterface;
use Pucene\Component\Math\MathExpressionBuilder;
use Pucene\Component\Pucene\Compiler\ElementInterface;
use Pucene\Component\Pucene\Dbal\Interpreter\InterpreterInterface;
use Pucene\Component\Pucene\Dbal\Interpreter\PuceneQueryBuilder;
use Pucene\Component\Symfony\Pool\PoolInterface;

class Coord implements ExpressionInterface
{
    /**
     * @var ElementInterface[]
     */
    private $elements;

    /**
     * @var PoolInterface
     */
    private $interpreterPool;

    /**
     * @var MathExpressionBuilder
     */
    private $expr;

    /**
     * @var PuceneQueryBuilder
     */
    private $queryBuilder;

    /**
     * @param ElementInterface[] $elements
     * @param PoolInterface $interpreterPool
     * @param PuceneQueryBuilder $queryBuilder
     * @param MathExpressionBuilder $expr
     */
    public function __construct(
        array $elements,
        PoolInterface $interpreterPool,
        PuceneQueryBuilder $queryBuilder,
        MathExpressionBuilder $expr
    ) {
        $this->elements = $elements;
        $this->interpreterPool = $interpreterPool;
        $this->queryBuilder = $queryBuilder;
        $this->expr = $expr;
    }

    public function __toString(): string
    {
        $sum = [];
        foreach ($this->elements as $element) {
            /** @var InterpreterInterface $interpreter */
            $interpreter = $this->interpreterPool->get(get_class($element));

            $expression = $interpreter->interpret($element, $this->queryBuilder);

            $sum[] = new IfCondition($expression, 1, 0);
        }

        return $this->expr->devide(call_user_func_array([$this->expr, 'add'], $sum), new Value(count($this->elements)));
    }
}
