<?php

namespace Pucene\Component\Elasticsearch\QueryBuilder\Query;

use Pucene\Component\Elasticsearch\QueryBuilder\QueryInterface;

/**
 * Represents a match_all query.
 */
class MatchAllBuilder implements QueryInterface
{
    public function toArray()
    {
        return [
            'match_all' => [],
        ];
    }
}
