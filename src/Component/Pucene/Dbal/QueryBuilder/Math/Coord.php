<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Math;

use Pucene\Component\Math\Expression\Value;
use Pucene\Component\Math\ExpressionInterface;
use Pucene\Component\Math\MathExpressionBuilder;
use Pucene\Component\Pucene\Dbal\QueryBuilder\Query\TermLevel\TermBuilder;
use Pucene\Component\Pucene\Dbal\QueryBuilder\ScoringQueryBuilder;

class Coord implements ExpressionInterface
{
    /**
     * @var TermBuilder[]
     */
    private $queries;

    /**
     * @var ScoringQueryBuilder
     */
    private $queryBuilder;

    /**
     * @var MathExpressionBuilder
     */
    private $expr;

    /**
     * @param TermBuilder[] $queries
     * @param ScoringQueryBuilder $queryBuilder
     * @param MathExpressionBuilder $expr
     */
    public function __construct(array $queries, ScoringQueryBuilder $queryBuilder, MathExpressionBuilder $expr)
    {
        $this->queries = $queries;
        $this->queryBuilder = $queryBuilder;
        $this->expr = $expr;
    }

    public function __toString(): string
    {
        $sum = [];
        foreach ($this->queries as $query) {
            $sum[] = $this->expr->count(
                $this->expr->variable($this->queryBuilder->joinTerm($query->getField(), $query->getTerm()) . '.id')
            );
        }

        return $this->expr->devide(call_user_func_array([$this->expr, 'add'], $sum), new Value(count($this->queries)));
    }
}
