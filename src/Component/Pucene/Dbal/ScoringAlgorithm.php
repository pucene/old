<?php

namespace Pucene\Component\Pucene\Dbal;

use Pucene\Component\Math\ExpressionInterface;
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

    public function __construct(PuceneQueryBuilder $queryBuilder, PuceneSchema $schema, PoolInterface $interpreterPool)
    {
        $this->queryBuilder = $queryBuilder;
        $this->schema = $schema;
        $this->interpreterPool = $interpreterPool;

        $this->math = new MathExpressionBuilder();
    }

    public function scoreTerm(TermElement $element, float $queryNorm = null, float $boost = 1): ExpressionInterface
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
     */
    public function queryNorm(array $termElements): float
    {
        $sum = 0;
        foreach ($termElements as $element) {
            $docCount = $this->getDocCountForElement($element);
            $sum += pow($this->calculateInverseDocumentFrequency($docCount), 2);
        }

        if (0 === $sum) {
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
     */
    private function calculateInverseDocumentFrequency(int $docCount): float
    {
        if (0 === $docCount) {
            return 0;
        }

        return 1 + log((float) $this->getDocCount() / ($docCount + 1));
    }

    private function getDocCountForElement(ElementInterface $element): int
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

    private function getDocCount(): int
    {
        if ($this->docCount) {
            return $this->docCount;
        }

        $queryBuilder = (new PuceneQueryBuilder($this->queryBuilder->getConnection(), $this->schema))
            ->select('count(document.id) as count')
            ->from($this->schema->getDocumentsTableName(), 'document');

        return $this->docCount = (int) $queryBuilder->execute()->fetchColumn();
    }

    public function getQueryBuilder(): PuceneQueryBuilder
    {
        return $this->queryBuilder;
    }
}
