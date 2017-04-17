<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Query\FullText;

use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Pucene\Component\Math\MathExpressionBuilder;
use Pucene\Component\Pucene\Dbal\QueryBuilder\Math\Coord;
use Pucene\Component\Pucene\Dbal\QueryBuilder\ParameterBag;
use Pucene\Component\Pucene\Dbal\QueryBuilder\Query\TermLevel\TermQuery;
use Pucene\Component\Pucene\Dbal\QueryBuilder\QueryInterface;
use Pucene\Component\Pucene\Dbal\QueryBuilder\ScoringQueryBuilder;

/**
 * Represents match query.
 */
class MatchQuery implements QueryInterface
{
    /**
     * @var TermQuery[]
     */
    private $queries;

    /**
     * @param array $queries
     */
    public function __construct(array $queries)
    {
        $this->queries = $queries;
    }

    public function build(ExpressionBuilder $expr, ParameterBag $parameter)
    {
        $or = $expr->orX();
        foreach ($this->queries as $query) {
            $or->add($query->build($expr, $parameter));
        }

        return $or;
    }

    public function scoring(MathExpressionBuilder $expr, ScoringQueryBuilder $queryBuilder)
    {
        $terms = array_map(
            function (TermQuery $query) {
                return $query->getTerm();
            },
            $this->queries
        );

        $expression = $expr->add();
        foreach ($this->queries as $query) {
            $queryNorm = $queryBuilder->queryNorm($query->getField(), $terms);
            $inverseDocumentFrequency = $queryBuilder->inverseDocumentFrequency($query->getField(), $query->getTerm());

            $expression->add(
                $expr->multiply(
                    $expr->value($queryNorm * $inverseDocumentFrequency),
                    new Coord($query->getField(), $terms, $queryBuilder, $expr),
                    $query->scoring($expr, $queryBuilder)
                )
            );
        }

        return $expression;
    }
}
