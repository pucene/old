<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder;

use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Doctrine\DBAL\Query\QueryBuilder;
use Pucene\Component\Math\MathExpressionBuilder;

interface QueryBuilderInterface
{
    /**
     * @param ExpressionBuilder $expr
     * @param QueryBuilder $queryBuilder
     *
     * @return mixed
     */
    public function build(ExpressionBuilder $expr, QueryBuilder $queryBuilder);

    public function scoring(MathExpressionBuilder $expr, ScoringQueryBuilder $queryBuilder);

    /**
     * @return QueryBuilderInterface[]
     */
    public function getTerms();
}
