<?php

namespace Pucene\Component\Elasticsearch\QueryBuilder\Query\TermLevel;

use Pucene\Component\Elasticsearch\QueryBuilder\QueryInterface;
use Pucene\Component\QueryBuilder\Query\TermLevel\TermQuery;

/**
 * Represents term query.
 */
class TermBuilder implements QueryInterface
{
    /**
     * @var TermQuery
     */
    private $query;

    /**
     * @param TermQuery $query
     */
    public function __construct(TermQuery $query)
    {
        $this->query = $query;
    }

    public function toArray()
    {
        return [
            'term' => [
                $this->query->getField() => $this->query->getTerm(),
            ],
        ];
    }
}
