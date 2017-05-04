<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Query\TermLevel;

use Pucene\Component\Pucene\Dbal\DbalStorage;
use Pucene\Component\Pucene\Dbal\QueryBuilder\QueryBuilderFactoryInterface;
use Pucene\Component\QueryBuilder\Query\QueryInterface as PuceneQueryInterface;

class IdsBuilderFactory implements QueryBuilderFactoryInterface
{
    public function build(PuceneQueryInterface $query, DbalStorage $storage)
    {
        return new IdsBuilder($query->getValues(), $query->getType());
    }
}
