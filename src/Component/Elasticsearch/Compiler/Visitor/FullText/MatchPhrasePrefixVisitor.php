<?php

namespace Pucene\Component\Elasticsearch\Compiler\Visitor\FullText;

use Pucene\Component\Elasticsearch\Compiler\VisitorInterface;
use Pucene\Component\QueryBuilder\Query\FullText\MatchPhrasePrefixQuery;
use Pucene\Component\QueryBuilder\Query\QueryInterface;

class MatchPhrasePrefixVisitor implements VisitorInterface
{
    /**
     * @param MatchPhrasePrefixQuery $query
     */
    public function visit(QueryInterface $query): array
    {
        return [MatchPhrasePrefixQuery::NAME => [$query->getField() => $query->getPhrase()]];
    }
}
