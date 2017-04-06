<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Pucene\Component\Math\MathExpressionBuilder;
use Pucene\Component\Pucene\Dbal\PuceneSchema;
use Pucene\Component\QueryBuilder\Search;

class SearchBuilder
{
    /**
     * @var QueryBuilderPool
     */
    private $builders;

    /**
     * @param QueryBuilderPool $builders
     */
    public function __construct(QueryBuilderPool $builders)
    {
        $this->builders = $builders;
    }

    /**
     * @param array $types
     * @param Search $search
     * @param PuceneSchema $schema
     * @param Connection $connection
     *
     * @return QueryBuilder
     */
    public function build(array $types, Search $search, PuceneSchema $schema, Connection $connection)
    {
        $queryBuilder = (new QueryBuilder($connection))->from($schema->getDocumentsTableName(), 'document')
            ->select(
                'document.*'
            )
            ->innerJoin('document', $schema->getFieldsTableName(), 'field', 'field.document_id = document.id')
            ->innerJoin('field', $schema->getTokensTableName(), 'token', 'token.field_id = field.id')
            ->where('document.type IN (?)')
            ->groupBy('document.id')
            ->orderBy('score', 'desc')
            ->setMaxResults($search->getSize())
            ->setFirstResult($search->getFrom())
            ->setParameter(0, implode(',', $types));

        $query = $this->builders->get(get_class($search->getQuery()))->build($search->getQuery());

        $expression = $query->build($queryBuilder->expr(), new ParameterBag($queryBuilder));
        if ($expression) {
            $queryBuilder->andWhere($expression);
        }

        $scoringQueryBuilder = new ScoringQueryBuilder($connection, $schema);
        $expression = $query->scoring(new MathExpressionBuilder(), $scoringQueryBuilder);

        $scoringQueryBuilder = $scoringQueryBuilder->getQueryBuilder();
        if ($expression) {
            $scoringQueryBuilder->select($expression);
            $queryBuilder->addSelect('(' . $scoringQueryBuilder->getSQL() . ') as score');
        } else {
            $queryBuilder->addSelect('1 as score');
        }

        return $queryBuilder;
    }
}
