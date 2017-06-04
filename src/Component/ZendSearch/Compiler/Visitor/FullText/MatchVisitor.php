<?php

namespace Pucene\Component\ZendSearch\Compiler\Visitor\FullText;

use Pucene\Component\Analysis\StandardAnalyzer;
use Pucene\Component\QueryBuilder\Query\FullText\MatchQuery;
use Pucene\Component\QueryBuilder\Query\QueryInterface;
use Pucene\Component\ZendSearch\Compiler\VisitorInterface;
use ZendSearch\Lucene\Index;
use ZendSearch\Lucene\Search\Query\MultiTerm;

class MatchVisitor implements VisitorInterface
{
    /**
     * {@inheritdoc}
     *
     * @param MatchQuery $query
     */
    public function visit(QueryInterface $query)
    {
        $analyzer = new StandardAnalyzer();

        $multiTerm = new MultiTerm();
        foreach ($analyzer->analyze($query->getQuery()) as $token) {
            $multiTerm->addTerm(new Index\Term($token->getTerm(), $query->getField()), null);
        }

        return $multiTerm;
    }
}
