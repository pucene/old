<?php

namespace Pucene\Component\Elasticsearch\QueryBuilder\Query\FullText;

use Pucene\Component\Elasticsearch\QueryBuilder\QueryInterface;
use Pucene\Component\QueryBuilder\Query\FullText\Match;

/**
 * Represents match query.
 */
class MatchQuery implements QueryInterface
{
    /**
     * @var Match
     */
    private $query;

    /**
     * @param Match $query
     */
    public function __construct(Match $query)
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
