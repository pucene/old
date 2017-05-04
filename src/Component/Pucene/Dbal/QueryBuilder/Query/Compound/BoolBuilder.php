<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Query\Compound;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Doctrine\DBAL\Query\QueryBuilder;
use Pucene\Component\Math\MathExpressionBuilder;
use Pucene\Component\Pucene\Dbal\PuceneSchema;
use Pucene\Component\Pucene\Dbal\QueryBuilder\Math\Coord;
use Pucene\Component\Pucene\Dbal\QueryBuilder\QueryBuilderInterface;
use Pucene\Component\Pucene\Dbal\QueryBuilder\ScoringQueryBuilder;

/**
 * Represents bool query.
 */
class BoolBuilder implements QueryBuilderInterface
{
    /**
     * @var QueryBuilderInterface[]
     */
    private $shouldQueries;

    /**
     * @var QueryBuilderInterface[]
     */
    private $mustQueries;

    /**
     * @var QueryBuilderInterface[]
     */
    private $mustNotQueries;

    /**
     * @var QueryBuilderInterface[]
     */
    private $filterQueries;

    /**
     * @var PuceneSchema
     */
    private $schema;

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(
        array $shouldQueries,
        array $mustQueries,
        array $mustNotQueries,
        array $filterQueries,
        PuceneSchema $schema,
        Connection $connection
    ) {
        $this->shouldQueries = $shouldQueries;
        $this->mustQueries = $mustQueries;
        $this->mustNotQueries = $mustNotQueries;
        $this->filterQueries = $filterQueries;
        $this->schema = $schema;
        $this->connection = $connection;
    }

    public function build(ExpressionBuilder $expr, QueryBuilder $queryBuilder)
    {
        $and = $expr->andX();
        foreach ($this->mustNotQueries as $query) {
            $and->add('NOT (' . $query->build($expr, $queryBuilder) . ')');
        }

        $mustQueries = array_merge($this->mustQueries, $this->filterQueries);
        if (count($mustQueries)) {
            foreach ($mustQueries as $query) {
                $and->add($query->build($expr, $queryBuilder));
            }

            return $and;
        }

        $or = $expr->orX();
        foreach ($this->shouldQueries as $query) {
            $or->add($query->build($expr, $queryBuilder));
        }

        $and->add($or);

        return $and;
    }

    public function scoring(MathExpressionBuilder $expr, ScoringQueryBuilder $queryBuilder, $queryNorm = null)
    {
        if (!$queryNorm) {
            $queryNorm = $queryBuilder->queryNorm($this->getTerms());
        }

        $queries = array_merge($this->shouldQueries, $this->mustQueries);

        $expression = $expr->add();
        foreach ($queries as $query) {
            $expression->add($query->scoring($expr, $queryBuilder, $queryNorm));
        }

        return $expr->multiply($expression, new Coord($queries, $this->schema, $this->connection, $expr));
    }

    public function getTerms()
    {
        $terms = [];
        $queries = array_merge($this->shouldQueries, $this->mustQueries);
        foreach ($queries as $query) {
            $terms = array_merge($terms, $query->getTerms());
        }

        return $terms;
    }
}
