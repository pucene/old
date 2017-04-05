<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder;

use Doctrine\DBAL\Query\QueryBuilder;
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
     * @param QueryBuilder $queryBuilder
     * @param Search $search
     *
     * @return QueryBuilder
     */
    public function build(QueryBuilder $queryBuilder, Search $search)
    {
        $query = $search->getQuery();

        $queryBuilder->setMaxResults($search->getSize())
            ->setFirstResult($search->getFrom());

        $builder = $this->builders->get(get_class($query))->build($query);
        $queryBuilder->andWhere($builder->build($queryBuilder->expr(), new ParameterBag($queryBuilder)));

        // TODO scoring

        return $queryBuilder;
    }
}
