<?php

namespace Pucene\Component\Pucene\Compiler\Visitor\FullText;

use Pucene\Component\Analysis\AnalyzerInterface;
use Pucene\Component\Pucene\Compiler\Element\CompositeElement;
use Pucene\Component\Pucene\Compiler\Element\TermElement;
use Pucene\Component\Pucene\Compiler\VisitorInterface;
use Pucene\Component\Pucene\StorageInterface;
use Pucene\Component\QueryBuilder\Query\FullText\MatchQuery;
use Pucene\Component\QueryBuilder\Query\QueryInterface;

class MatchVisitor implements VisitorInterface
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
    public function visit(QueryInterface $query, StorageInterface $storage)
    {
        $tokens = $this->analyzer->analyze($query->getQuery());

        $terms = [];
        foreach ($tokens as $token) {
            $terms[] = new TermElement($query->getField(), $token->getEncodedTerm(), 1, $query->getFuzzy());
        }

        return new CompositeElement(CompositeElement:: OR, $terms);
    }
}
