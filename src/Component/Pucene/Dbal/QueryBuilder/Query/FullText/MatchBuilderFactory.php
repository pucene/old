<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Query\FullText;

use Pucene\Component\Analysis\AnalyzerInterface;
use Pucene\Component\Pucene\Dbal\DbalStorage;
use Pucene\Component\Pucene\Dbal\QueryBuilder\Query\TermLevel\TermBuilder;
use Pucene\Component\Pucene\Dbal\QueryBuilder\Query\Compound\BoolBuilder;
use Pucene\Component\Pucene\Dbal\QueryBuilder\QueryBuilderInterface;
use Pucene\Component\QueryBuilder\Query\FullText\MatchQuery;
use Pucene\Component\QueryBuilder\Query\QueryInterface;

/**
 * Build match query.
 */
class MatchBuilderFactory implements QueryBuilderInterface
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
     *
     * @param MatchQuery $query
     */
    public function build(QueryInterface $query, DbalStorage $storage)
    {
        $tokens = $this->analyzer->analyze($query->getQuery());

        $terms = [];
        foreach ($tokens as $token) {
            $terms[] = new TermBuilder($query->getField(), $token->getEncodedTerm());
        }

        return new BoolBuilder($terms);
    }
}
