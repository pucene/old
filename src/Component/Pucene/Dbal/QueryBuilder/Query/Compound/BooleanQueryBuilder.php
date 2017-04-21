<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Query\Compound;

use Pucene\Component\Analysis\AnalyzerInterface;
use Pucene\Component\Pucene\Dbal\DbalStorage;
use Pucene\Component\Pucene\Dbal\QueryBuilder\Query\TermLevel\TermQuery;
use Pucene\Component\Pucene\Dbal\QueryBuilder\QueryBuilderInterface;
use Pucene\Component\QueryBuilder\Query\QueryInterface;

/**
 * Build bool query.
 */
class BooleanQueryBuilder implements QueryBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function build(QueryInterface $query, DbalStorage $storage)
    {
        return new BooleanQuery($query);
    }
}
