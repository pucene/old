<?php

namespace Pucene\Component\Pucene\Dbal;

use Pucene\Component\Math\ExpressionInterface;
use Pucene\Component\Math\MathExpressionBuilder;
use Pucene\Component\Pucene\Compiler\Element\TermElement;
use Pucene\Component\Pucene\Compiler\ElementInterface;
use Pucene\Component\Pucene\Dbal\Interpreter\PuceneQueryBuilder;
use Pucene\Component\Pucene\Dbal\Math\FieldLength;
use Pucene\Component\Pucene\Dbal\Math\IfCondition;
use Pucene\Component\Pucene\Dbal\Math\TermFrequency;
use Pucene\Component\Symfony\Pool\PoolInterface;

class ScoringAlgorithm
{
    const K1 = 1.2;
    const B = 0.75;

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

    public function scoreTerm(TermElement $element): ExpressionInterface
    {
        $avgFieldLength = $this->averageFieldLength($element->getField());
        $idf = $this->inverseDocumentFrequency($element);

        $alias = $this->queryBuilder->joinTerm($element->getField(), $element->getTerm());

        $expression = $this->math->multiply(
            $this->math->devide(
                $this->math->multiply(
                    new TermFrequency($alias, $this->math),
                    $this->math->value(self::K1 + 1)
                ),
                $this->math->add(
                    new TermFrequency($alias, $this->math),
                    $this->math->multiply(
                        $this->math->value(self::K1),
                        $this->math->add(
                            $this->math->value(1 - self::B),
                            $this->math->multiply(
                                $this->math->value(self::B),
                                $this->math->devide(
                                    new FieldLength($alias, $this->math),
                                    $avgFieldLength
                                )
                            )
                        )
                    )
                )
            ),
            $this->math->value($idf)
        );

        return new IfCondition(sprintf('%s.term=\'%s\'', $alias, $element->getTerm()), $expression, 0);
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

        return log(1.0 + ($this->getDocCount() - $docCount + 0.5) / ($docCount + 0.5));
    }

    private function averageFieldLength(string $fieldName)
    {
        $queryBuilder = (new PuceneQueryBuilder($this->queryBuilder->getConnection(), $this->schema))
            ->select('SUM(field.field_length)/COUNT(*)')
            ->from($this->schema->getFieldsTableName(), 'field')
            ->where('field.field_name = :fieldName')
            ->setParameter('fieldName', $fieldName);

        return (float) $queryBuilder->execute()->fetchColumn();
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
