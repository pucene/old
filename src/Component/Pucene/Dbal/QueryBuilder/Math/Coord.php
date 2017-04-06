<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Math;

use Pucene\Component\Math\Expression\Value;
use Pucene\Component\Math\ExpressionInterface;
use Pucene\Component\Math\MathExpressionBuilder;
use Pucene\Component\Pucene\Dbal\QueryBuilder\ScoringQueryBuilder;

class Coord implements ExpressionInterface
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var string[]
     */
    private $terms;

    /**
     * @var ScoringQueryBuilder
     */
    private $queryBuilder;

    /**
     * @var MathExpressionBuilder
     */
    private $expr;

    /**
     * @param string $field
     * @param string[] $terms
     * @param ScoringQueryBuilder $queryBuilder
     * @param MathExpressionBuilder $expr
     */
    public function __construct(
        string $field,
        array $terms,
        ScoringQueryBuilder $queryBuilder,
        MathExpressionBuilder $expr
    ) {
        $this->field = $field;
        $this->terms = $terms;
        $this->queryBuilder = $queryBuilder;
        $this->expr = $expr;
    }

    public function __toString(): string
    {
        $sum = [];
        foreach ($this->terms as $term) {
            $sum[] = $this->expr->count(
                $this->expr->variable($this->queryBuilder->joinTerm($this->field, $term) . '.id')
            );
        }

        return $this->expr->devide(call_user_func_array([$this->expr, 'add'], $sum), new Value(count($this->terms)));
    }
}
