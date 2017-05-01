<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Query;

use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Pucene\Component\Math\MathExpressionBuilder;
use Pucene\Component\Pucene\Dbal\QueryBuilder\ParameterBag;
use Pucene\Component\Pucene\Dbal\QueryBuilder\QueryInterface;
use Pucene\Component\Pucene\Dbal\QueryBuilder\ScoringQueryBuilder;

/**
 * Represents a match_all query.
 */
class MatchAllBuilder implements QueryInterface
{
    public function build(ExpressionBuilder $expr, ParameterBag $parameter)
    {
        // no expression
    }

    public function scoring(MathExpressionBuilder $expr, ScoringQueryBuilder $queryBuilder)
    {
        // no expression
    }
}
