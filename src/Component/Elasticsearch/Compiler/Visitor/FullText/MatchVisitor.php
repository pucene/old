<?php

namespace Pucene\Component\Elasticsearch\Compiler\Visitor\FullText;

use Pucene\Component\Elasticsearch\Compiler\VisitorInterface;
use Pucene\Component\QueryBuilder\Query\FullText\MatchQuery;
use Pucene\Component\QueryBuilder\Query\QueryInterface;

class MatchVisitor implements VisitorInterface
{
    /**
     * @param MatchQuery $query
     */
    public function visit(QueryInterface $query): array
    {
        $parameters = ['query' => $query->getQuery()];
        if ($query->getFuzzy()) {
            $parameters['fuzziness'] = $query->getFuzzy();
        }

        return ['match' => [$query->getField() => $parameters]];
    }
}
