<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Query\Compound;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Pucene\Component\Math\MathExpressionBuilder;
use Pucene\Component\Pucene\Dbal\PuceneSchema;
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

    /**
     * @var PuceneSchema
     */
    private $schema;

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(array $shouldQueries, PuceneSchema $schema, Connection $connection)
    {
        $this->shouldQueries = $shouldQueries;
        $this->schema = $schema;
        $this->connection = $connection;
    }

    public function build(ExpressionBuilder $expr, ParameterBag $parameter)
    {
        $or = $expr->orX();
        foreach ($this->shouldQueries as $query) {
            $or->add($query->build($expr, $parameter));
        }

        return $or;
    }

    public function scoring(MathExpressionBuilder $expr, ScoringQueryBuilder $queryBuilder, $queryNorm = null)
    {
        if (!$queryNorm) {
            $queryNorm = $queryBuilder->queryNorm($this->getTerms());
        }

        $expression = $expr->add();
        foreach ($this->shouldQueries as $query) {
            $expression->add($query->scoring($expr, $queryBuilder, $queryNorm));
        }

        return $expr->multiply($expression, new Coord($this->shouldQueries, $this->schema, $this->connection, $expr));
    }

    public function getTerms()
    {
        $terms = [];
        foreach ($this->shouldQueries as $query) {
            $terms = array_merge($terms, $query->getTerms());
        }

        return $terms;
    }
}
