<?php

namespace Pucene\Component\Pucene\Compiler\Visitor\FullText;

use Doctrine\DBAL\Query\QueryBuilder;
use Pucene\Component\Analysis\AnalyzerInterface;
use Pucene\Component\Analysis\Token;
use Pucene\Component\Pucene\Compiler\Element\CompositeElement;
use Pucene\Component\Pucene\Compiler\Element\TermElement;
use Pucene\Component\Pucene\Compiler\VisitorInterface;
use Pucene\Component\Pucene\Dbal\DbalStorage;
use Pucene\Component\Pucene\Dbal\Interpreter\Fuzzy;
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
        if ($query->getFuzzy()) {
            return $this->visitFuzzy($tokens, $query, $storage);
        }

        $terms = [];
        foreach ($tokens as $token) {
            $terms[] = new TermElement($query->getField(), $token->getEncodedTerm(), 1, $query->getFuzzy());
        }

        return new CompositeElement(CompositeElement::OR, $terms);
    }

    /**
     * Visit fuzzy match.
     *
     * @param Token[] $tokens
     * @param MatchQuery $query
     * @param DbalStorage $storage
     *
     * @return CompositeElement
     */
    private function visitFuzzy(array $tokens, MatchQuery $query, DbalStorage $storage)
    {
        $terms = [];
        foreach ($tokens as $token) {
            $terms[] = new TermElement($query->getField(), $token->getTerm());
            $terms = array_merge($terms, $this->findFuzzyTerms($token->getEncodedTerm(), $query, $storage));
        }

        return new CompositeElement(CompositeElement::OR, $terms, 1, false);
    }

    /**
     * Find fuzzy terms.
     *
     * @param string $term
     * @param MatchQuery $query
     * @param DbalStorage $storage
     *
     * @return TermElement[]
     */
    private function findFuzzyTerms(string $term, MatchQuery $query, DbalStorage $storage)
    {
        $queryBuilder = (new QueryBuilder($storage->getConnection()))
            ->select('DISTINCT term.term')
            ->from($storage->getSchema()->getDocumentTermsTableName(), 'term');

        foreach (Fuzzy::getFuzzyTerms($term, $query->getFuzzy()) as $term) {
            $queryBuilder->orWhere($queryBuilder->expr()->like('term.term', "'" . $term . "'"));
        }

        return array_map(
            function ($item) use ($query) {
                return new TermElement($query->getField(), $item['term']);
            },
            $queryBuilder->execute()->fetchAll()
        );
    }
}
