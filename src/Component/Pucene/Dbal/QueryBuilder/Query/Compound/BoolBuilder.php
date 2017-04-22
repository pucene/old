<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Query\Compound;

use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Pucene\Component\Math\MathExpressionBuilder;
use Pucene\Component\Pucene\Dbal\QueryBuilder\Math\Coord;
use Pucene\Component\Pucene\Dbal\QueryBuilder\ParameterBag;
use Pucene\Component\Pucene\Dbal\QueryBuilder\QueryInterface;
use Pucene\Component\Pucene\Dbal\QueryBuilder\ScoringQueryBuilder;

/**
 * Represents bool query.
 */
class BoolBuilder implements QueryInterface
{
    /**
     * @var QueryInterface
     */
    private $shouldQueries;

    public function __construct(array $shouldQueries)
    {
        $this->shouldQueries = $shouldQueries;
    }

    public function build(ExpressionBuilder $expr, ParameterBag $parameter)
    {
        $or = $expr->orX();
        foreach ($this->shouldQueries as $query) {
            $or->add($query->build($expr, $parameter));
        }

        return $or;
    }

    public function scoring(MathExpressionBuilder $expr, ScoringQueryBuilder $queryBuilder)
    {
        $queryNorm = $queryBuilder->queryNorm($this->shouldQueries);

        $expression = $expr->add();
        foreach ($this->shouldQueries as $query) {
            $inverseDocumentFrequency = $queryBuilder->inverseDocumentFrequency($query->getField(), $query->getTerm());

            $expression->add(
                $expr->multiply(
                    $expr->value($queryNorm),
                    $expr->value($inverseDocumentFrequency),
                    $query->scoring($expr, $queryBuilder)
                )
            );
        }

        return $expr->multiply($expression, new Coord($this->shouldQueries, $queryBuilder, $expr));
    }
}
