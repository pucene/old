<?php

namespace Pucene\Component\Pucene\Compiler\Visitor\FullText;

use Doctrine\DBAL\Query\QueryBuilder;
use Pucene\Component\Analysis\AnalyzerInterface;
use Pucene\Component\Analysis\Token;
use Pucene\Component\Pucene\Compiler\Element\CompositeElement;
use Pucene\Component\Pucene\Compiler\Element\TermElement;
use Pucene\Component\Pucene\Compiler\ElementInterface;
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

    public function __construct(AnalyzerInterface $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    /**
     * @param MatchQuery $query
     */
    public function visit(QueryInterface $query, StorageInterface $storage): ?ElementInterface
    {
        $tokens = $this->analyzer->analyze($query->getQuery());
        if ($query->getFuzzy()) {
            return $this->visitFuzzy($tokens, $query, $storage);
        }

        $terms = [];
        foreach ($tokens as $token) {
            $terms[] = new TermElement($query->getField(), $token->getEncodedTerm());
        }

        return new CompositeElement(CompositeElement::OR, $terms);
    }

    /**
     * @param Token[] $tokens
     */
    private function visitFuzzy(array $tokens, MatchQuery $query, DbalStorage $storage): ElementInterface
    {
        $terms = [];
        foreach ($tokens as $token) {
            $terms[] = new TermElement($query->getField(), $token->getTerm());
            $terms = array_merge($terms, $this->findFuzzyTerms($token->getEncodedTerm(), $query, $storage));
        }

        return new CompositeElement(CompositeElement::OR, $terms, 1);
    }

    /**
     * @return TermElement[]
     */
    private function findFuzzyTerms(string $term, MatchQuery $query, DbalStorage $storage): array
    {
        $queryBuilder = (new QueryBuilder($storage->getConnection()))
            ->select('DISTINCT term.term')
            ->from($storage->getSchema()->getTermsTableName(), 'term')
            ->where(
                sprintf(
                    '(LENGTH(term.term) - %1$s) BETWEEN -%2$s AND %2$s',
                    strlen($term),
                    Fuzzy::getFuzzyDistance($term, $query->getFuzzy())
                )
            );

        $orX = $queryBuilder->expr()->orX();
        foreach (Fuzzy::getFuzzyTerms($term, $query->getFuzzy()) as $term) {
            $orX->add($queryBuilder->expr()->like('term.term', "'" . $term . "'"));
        }
        $queryBuilder->andWhere($orX);

        return array_map(
            function ($item) use ($query) {
                return new TermElement($query->getField(), $item['term']);
            },
            $queryBuilder->execute()->fetchAll()
        );
    }
}
