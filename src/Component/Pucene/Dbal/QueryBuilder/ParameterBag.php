<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder;

use Doctrine\DBAL\Query\QueryBuilder;

class ParameterBag
{
    /**
     * @var int
     */
    private $index;

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
        $this->index = count($queryBuilder->getParameters());
    }

    public function add($value)
    {
        $this->queryBuilder->setParameter($this->index++, $value);
    }
}
