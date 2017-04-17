<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Pucene\Component\Pucene\Dbal\PuceneSchema;

class ScoringQueryBuilder
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var PuceneSchema
     */
    private $schema;

    /**
     * @var array
     */
    private $joins = [];

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var int
     */
    private $docCount;

    /**
     * @var int[]
     */
    private $docCountPerTerm = [];

    /**
     * @param Connection $connection
     * @param PuceneSchema $schema
     */
    public function __construct(Connection $connection, PuceneSchema $schema)
    {
        $this->connection = $connection;
        $this->schema = $schema;

        $this->queryBuilder = new QueryBuilder($connection);
        $this->queryBuilder->from($schema->getDocumentsTableName(), 'innerDocument')
            ->where('innerDocument.id = document.id')
            ->groupBy('innerDocument.id');
    }

    public function inverseDocumentFrequency(string $field, string $term): float
    {
        return $this->calculateInverseDocumentFrequency($this->getDocCountPerTerm($field, $term));
    }

    public function inverseDocumentFrequencyPerDocument(string $term): float
    {
        return $this->calculateInverseDocumentFrequency($this->getDocCountPerTermPerDocument($term));
    }

    public function queryNorm(string $field, array $terms): float
    {
        $sum = 0;
        foreach ($this->getDocCountPerTerms($field, $terms) as $term => $value) {
            $sum += pow($this->calculateInverseDocumentFrequency($value), 2);
        }

        if ($sum === 0) {
            return 0;
        }

        return 1.0 / sqrt($sum);
    }

    public function joinTerm(string $field, string $term): string
    {
        $termName = $term . 'Term';
        if (in_array($termName, $this->joins)) {
            return $termName;
        }

        $fieldName = $this->joinField($field);
        $this->queryBuilder->leftJoin(
            $fieldName,
            $this->schema->getFieldTermsTableName(),
            $termName,
            sprintf('%s.field_id = %s.id and %s.term = \'%s\'', $termName, $fieldName, $termName, $term)
        );

        return $this->joins[] = $termName;
    }

    public function joinField(string $field): string
    {
        $fieldName = $field . 'Field';
        if (in_array($fieldName, $this->joins)) {
            return $fieldName;
        }

        $this->queryBuilder->leftJoin(
            'innerDocument',
            $this->schema->getFieldsTableName(),
            $fieldName,
            sprintf('innerDocument.id = %s.document_id and %s.name = \'%s\'', $fieldName, $fieldName, $field)
        );

        return $this->joins[] = $fieldName;
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    private function calculateInverseDocumentFrequency($docCount)
    {
        return 1 + log((float) $this->getDocCount() / ($docCount + 1));
    }

    private function getDocCount()
    {
        if ($this->docCount) {
            return $this->docCount;
        }

        $queryBuilder = (new QueryBuilder($this->connection))
            ->select('count(document.id) as count')
            ->from($this->schema->getDocumentsTableName(), 'document');

        return $this->docCount = (int) $queryBuilder->execute()->fetchColumn();
    }

    private function getDocCountPerTerm(string $field, string $term)
    {
        if (array_key_exists($term, $this->docCountPerTerm)) {
            return $this->docCountPerTerm[$term];
        }

        $queryBuilder = (new QueryBuilder($this->connection))
            ->select('count(document.id) as count')
            ->from($this->schema->getDocumentsTableName(), 'document')
            ->innerJoin(
                'document',
                $this->schema->getFieldsTableName(),
                'field',
                sprintf('document.id = field.document_id and field.name = \'%s\'', $field)
            )->innerJoin(
                'field',
                $this->schema->getFieldTermsTableName(),
                'term',
                sprintf('field.id = term.field_id and term.term = \'%s\'', $term)
            );

        return $this->docCountPerTerm[$term] = (int) $queryBuilder->execute()->fetchColumn();
    }

    public function getDocCountPerTermPerDocument(string $term)
    {
        if (array_key_exists($term, $this->docCountPerTerm)) {
            return $this->docCountPerTerm[$term];
        }

        $queryBuilder = (new QueryBuilder($this->connection))
            ->select('count(document.id) as count')
            ->from($this->schema->getDocumentsTableName(), 'document')
            ->innerJoin(
                'document',
                $this->schema->getFieldsTableName(),
                'field',
                'document.id = field.document_id'
            )->innerJoin(
                'field',
                $this->schema->getFieldTermsTableName(),
                'term',
                sprintf('field.id = term.field_id and term.term = \'%s\'', $term)
            );

        return $this->docCountPerTerm[$term] = (int) $queryBuilder->execute()->fetchColumn();
    }

    private function getDocCountPerTerms(string $field, array $terms)
    {
        $inExpression = $this->connection->getExpressionBuilder()->in(
            'fieldTerm.term',
            "'" . implode("','", $terms) . "'"
        );

        $queryBuilder = (new QueryBuilder($this->connection))
            ->select('count(document.id) as count')->addSelect('fieldTerm.term as term')
            ->from($this->schema->getDocumentsTableName(), 'document')
            ->innerJoin(
                'document',
                $this->schema->getFieldsTableName(),
                'field',
                sprintf('document.id = field.document_id and field.name = \'%s\'', $field)
            )->innerJoin(
                'field',
                $this->schema->getFieldTermsTableName(),
                'fieldTerm',
                sprintf('field.id = fieldTerm.field_id and %s', $inExpression)
            )->groupBy('fieldTerm.term');

        $result = [];
        foreach ($queryBuilder->execute() as $item) {
            $this->docCountPerTerm[$item['term']] = $result[$item['term']] = $item['count'];
        }

        return $result;
    }
}
