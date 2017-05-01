<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Query\TermLevel;

use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Doctrine\DBAL\Query\QueryBuilder;
use Pucene\Component\Math\MathExpressionBuilder;
use Pucene\Component\Pucene\Dbal\QueryBuilder\Math\FieldLengthNorm;
use Pucene\Component\Pucene\Dbal\QueryBuilder\Math\TermFrequency;
use Pucene\Component\Pucene\Dbal\QueryBuilder\QueryBuilderInterface;
use Pucene\Component\Pucene\Dbal\QueryBuilder\ScoringQueryBuilder;

/**
 * Represents term query.
 */
class TermBuilder implements QueryBuilderInterface
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $term;

    /**
     * @var float
     */
    private $boost;

    /**
     * @param string $field
     * @param string $term
     * @param float $boost
     */
    public function __construct(string $field, string $term, float $boost = 1)
    {
        $this->field = $field;
        $this->term = $term;
        $this->boost = $boost;
    }

    public function getField()
    {
        return $this->field;
    }

    public function getTerm()
    {
        return $this->term;
    }

    public function build(ExpressionBuilder $expr, QueryBuilder $queryBuilder)
    {
        $fieldName = 'field' . ucfirst($this->field) . uniqid();
        $termName = 'field' . ucfirst($this->term) . uniqid();

        $queryBuilder->leftJoin(
                'document',
                'pu_my_index_fields',
                $fieldName,
                $fieldName . '.document_id = document.id AND ' . $fieldName . '.name = \'' . $this->field . '\''
            )->leftJoin(
                $fieldName,
                'pu_my_index_tokens',
                $termName,
                $termName . '.field_id = ' . $fieldName . '.id AND ' . $termName . '.term = \'' . $this->term . '\''
            );

        return $expr->isNotNull($termName . '.id');
    }

    public function scoring(MathExpressionBuilder $expr, ScoringQueryBuilder $queryBuilder, $queryNorm = null)
    {
        $inverseDocumentFrequency = $queryBuilder->inverseDocumentFrequency($this);

        $expression = $expr->multiply(
            new TermFrequency($this->getField(), $this->getTerm(), $queryBuilder, $expr),
            $expr->value($inverseDocumentFrequency),
            new FieldLengthNorm($this->getField(), $queryBuilder, $expr),
            $this->boost
        );

        if ($queryNorm) {
            $expression->add($expr->value($queryNorm * $inverseDocumentFrequency));
        }

        return $expression;
    }

    public function getTerms()
    {
        return [$this];
    }
}
