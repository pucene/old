<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Query\Compound;

use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Pucene\Component\Math\MathExpressionBuilder;
use Pucene\Component\Pucene\Dbal\QueryBuilder\Math\Coord;
use Pucene\Component\Pucene\Dbal\QueryBuilder\ParameterBag;
use Pucene\Component\Pucene\Dbal\QueryBuilder\Query\TermLevel\TermQuery;
use Pucene\Component\Pucene\Dbal\QueryBuilder\QueryInterface;
use Pucene\Component\Pucene\Dbal\QueryBuilder\ScoringQueryBuilder;
use Pucene\Component\QueryBuilder\Query\Compound\Boolean;

/**
 * Represents bool query.
 */
class BooleanQuery implements QueryInterface
{
    /**
     * @var Boolean
     */
    private $query;

    /**
     * @param Boolean $query
     */
    public function __construct(Boolean $query)
    {
        $this->query = $query;
    }

    public function build(ExpressionBuilder $expr, ParameterBag $parameter)
    {

    }

    public function scoring(MathExpressionBuilder $expr, ScoringQueryBuilder $queryBuilder)
    {

    }
}
