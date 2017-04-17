<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder;

use Doctrine\DBAL\Query\QueryBuilder;
use Pucene\Component\Math\MathExpressionBuilder;
use Pucene\Component\Pucene\Dbal\DbalStorage;
use Pucene\Component\QueryBuilder\Search;
use Pucene\Component\QueryBuilder\Sort\IdSort;

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
     * @param DbalStorage $storage
     *
     * @return QueryBuilder
     */
    public function build(array $types, Search $search, DbalStorage $storage)
    {
        $connection = $storage->getConnection();
        $schema = $storage->getSchema();

        $queryBuilder = (new QueryBuilder($connection))
            ->from($schema->getDocumentsTableName(), 'document')
            ->select('document.*')
            ->innerJoin('document', $schema->getFieldsTableName(), 'field', 'field.document_id = document.id')
            ->innerJoin('field', $schema->getTokensTableName(), 'token', 'token.field_id = field.id')
            ->where('document.type IN (?)')
            ->groupBy('document.id')
            ->setMaxResults($search->getSize())
            ->setFirstResult($search->getFrom())
            ->setParameter(0, implode(',', $types));

        $query = $this->builders->get(get_class($search->getQuery()))->build($search->getQuery(), $storage);

        $expression = $query->build($queryBuilder->expr(), new ParameterBag($queryBuilder));
        if ($expression) {
            $queryBuilder->andWhere($expression);
        }

        $scoringQueryBuilder = $storage->createScoringQueryBuilder();
        $expression = $query->scoring(new MathExpressionBuilder(), $scoringQueryBuilder);

        $scoringQueryBuilder = $scoringQueryBuilder->getQueryBuilder();
        if ($expression) {
            $scoringQueryBuilder->select($expression);
            $queryBuilder->addSelect('(' . $scoringQueryBuilder->getSQL() . ') as score')->orderBy('score', 'desc');
        } else {
            $queryBuilder->addSelect('1 as score');
        }

        if (0 < count($search->getSorts())) {
            foreach ($search->getSorts() as $sort) {
                if ($sort instanceof IdSort) {
                    $queryBuilder->addOrderBy('id', $sort->getOrder());
                }
            }
        }

        return $queryBuilder;
    }
}
