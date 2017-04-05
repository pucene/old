<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Query\FullText;

use Pucene\Component\Analysis\AnalyzerInterface;
use Pucene\Component\Pucene\Dbal\QueryBuilder\Query\TermLevel\TermQuery;
use Pucene\Component\Pucene\Dbal\QueryBuilder\QueryBuilderInterface;
use Pucene\Component\QueryBuilder\Query\QueryInterface;
use Pucene\Component\QueryBuilder\Query\TermLevel\Term;

/**
 * Build match query.
 */
class MatchQueryBuilder implements QueryBuilderInterface
{
    /**
     * @var AnalyzerInterface
     */
    private $analyzer;

    /**
     * @param AnalyzerInterface $analyzer
     */
    public function __construct(AnalyzerInterface $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    /**
     * {@inheritdoc}
     */
    public function build(QueryInterface $query)
    {
        $tokens = $this->analyzer->analyze($query->getQuery());

        $terms = [];
        foreach ($tokens as $token) {
            $terms[] = new TermQuery(new Term($query->getField(), $token->getTerm()));
        }

        return new MatchQuery($terms);
    }
}
