<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder;

use Pucene\Component\Pucene\Dbal\DbalStorage;
use Pucene\Component\QueryBuilder\Query\QueryInterface as PuceneQueryInterface;

interface QueryBuilderFactoryInterface
{
    /**
     * @param PuceneQueryInterface $query
     * @param DbalStorage $storage
     *
     * @return QueryBuilderInterface
     */
    public function build(PuceneQueryInterface $query, DbalStorage $storage);
}
