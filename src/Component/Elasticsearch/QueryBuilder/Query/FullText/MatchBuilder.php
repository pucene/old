<?php

namespace Pucene\Component\Elasticsearch\QueryBuilder\Query\FullText;

use Pucene\Component\Elasticsearch\QueryBuilder\QueryInterface;
use Pucene\Component\QueryBuilder\Query\FullText\MatchQuery;

/**
 * Represents match query.
 */
class MatchBuilder implements QueryInterface
{
    /**
     * @var MatchQuery
     */
    private $query;

    /**
     * @param MatchQuery $query
     */
    public function __construct(MatchQuery $query)
    {
        $this->query = $query;
    }

    public function toArray()
    {
        return [
            'match' => [
                $this->query->getField() => $this->query->getQuery(),
            ],
        ];
    }
}
