<?php

namespace Pucene\Component\Elasticsearch\QueryBuilder\Query\TermLevel;

use Pucene\Component\Elasticsearch\QueryBuilder\QueryInterface;
use Pucene\Component\QueryBuilder\Query\TermLevel\IdsQuery;

class IdsBuilder implements QueryInterface
{
    /**
     * @var IdsQuery
     */
    private $query;

    /**
     * @param IdsQuery $query
     */
    public function __construct(IdsQuery $query)
    {
        $this->query = $query;
    }

    public function toArray()
    {
        return ['ids' => ['values' => $this->query->getValues(), 'type' => $this->query->getType()]];
    }
}
