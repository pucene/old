<?php

namespace Pucene\Component\Pucene\Dbal;

use Pucene\Component\Math\MathExpressionBuilder;
use Pucene\Component\Pucene\Compiler\Element\TermElement;
use Pucene\Component\Pucene\Compiler\ElementInterface;
use Pucene\Component\Pucene\Dbal\Interpreter\PuceneQueryBuilder;
use Pucene\Component\Pucene\Dbal\Math\FieldLengthNorm;
use Pucene\Component\Pucene\Dbal\Math\IfCondition;
use Pucene\Component\Pucene\Dbal\Math\TermFrequency;
use Pucene\Component\Symfony\Pool\PoolInterface;

class ScoringAlgorithm
{
    /**
     * @var MathExpressionBuilder
     */
    private $math;

    /**
     * @var PuceneQueryBuilder
     */
    private $queryBuilder;

    /**
     * @var PuceneSchema
     */
    private $schema;

    /**
     * @var PoolInterface
     */
    private $interpreterPool;

    /**
     * @var int
     */
    private $docCount;

    /**
     * @param PuceneQueryBuilder $queryBuilder
     * @param PuceneSchema $schema
     * @param PoolInterface $interpreterPool
     */
    public function __construct(PuceneQueryBuilder $queryBuilder, PuceneSchema $schema, PoolInterface $interpreterPool)
    {
        $this->queryBuilder = $queryBuilder;
        $this->schema = $schema;
        $this->interpreterPool = $interpreterPool;

        $this->math = new MathExpressionBuilder();
    }

    public function scoreTerm(TermElement $element, float $queryNorm = null, float $boost = 1)
    {
        $idf = $this->inverseDocumentFrequency($element);

        $factor = $idf * $element->getBoost();
        if ($queryNorm) {
            $factor *= $idf * $queryNorm;
        }

        $alias = $this->queryBuilder->joinTerm($element->getField(), $element->getTerm());

        $expression = $this->math->multiply(
            new TermFrequency($alias, $this->math),
            new FieldLengthNorm($alias, $this->math),
            $this->math->value($factor)
        );

        return new IfCondition(
            sprintf('%s.term=\'%s\'', $alias, $element->getTerm()),
            $expression,
            $this->math->multiply($this->math->value(0.6), $expression)
        );
    }

    /**
     * @param TermElement[] $termElements
     *
     * @return float
     */
    public function queryNorm(array $termElements)
    {
        $sum = 0;
        foreach ($termElements as $element) {
            $docCount = $this->getDocCountForElement($element);
            $sum += pow($this->calculateInverseDocumentFrequency($docCount), 2);
        }

        if ($sum === 0) {
            return 0;
        }

        return 1.0 / sqrt($sum);
    }

    private function inverseDocumentFrequency(ElementInterface $element): float
    {
        return $this->calculateInverseDocumentFrequency($this->getDocCountForElement($element));
    }

    /**
     * @param int $docCount
     *
     * @return float
     */
    private function calculateInverseDocumentFrequency(int $docCount)
    {
        if ($docCount === 0) {
            return 0;
        }

        return 1 + log((float) $this->getDocCount() / ($docCount + 1));
    }

    private function getDocCountForElement(ElementInterface $element)
    {
        $queryBuilder = (new PuceneQueryBuilder($this->queryBuilder->getConnection(), $this->schema))
            ->select('count(document.id) as count')
            ->from($this->schema->getDocumentsTableName(), 'document');

        $expression = $this->interpreterPool->get(get_class($element))->interpret($element, $queryBuilder);
        if ($expression) {
            $queryBuilder->where($expression);
        }

        return (int) $queryBuilder->execute()->fetchColumn();
    }

    private function getDocCount()
    {
        if ($this->docCount) {
            return $this->docCount;
        }

        $queryBuilder = (new PuceneQueryBuilder($this->queryBuilder->getConnection(), $this->schema))
            ->select('count(document.id) as count')
            ->from($this->schema->getDocumentsTableName(), 'document');

        return $this->docCount = (int) $queryBuilder->execute()->fetchColumn();
    }

    /**
     * Returns queryBuilder.
     *
     * @return PuceneQueryBuilder
     */
    public function getQueryBuilder(): PuceneQueryBuilder
    {
        return $this->queryBuilder;
    }
}
