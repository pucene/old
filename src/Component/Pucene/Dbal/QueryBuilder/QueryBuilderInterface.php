<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder;

use Pucene\Component\QueryBuilder\Query\QueryInterface as PuceneQueryInterface;

interface QueryBuilderInterface
{
    /**
     * @param PuceneQueryInterface $query
     *
     * @return QueryInterface
     */
    public function build(PuceneQueryInterface $query);
}
