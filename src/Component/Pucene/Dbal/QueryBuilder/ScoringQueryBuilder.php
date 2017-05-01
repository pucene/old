<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Pucene\Component\Pucene\Dbal\PuceneSchema;
use Pucene\Component\Pucene\Dbal\QueryBuilder\Query\TermLevel\TermBuilder;

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

    public function inverseDocumentFrequency(QueryBuilderInterface $query): float
    {
        return $this->calculateInverseDocumentFrequency($this->getDocCountForQuery($query));
    }

    /**
     * @param TermBuilder[] $queries
     *
     * @return float
     */
    public function queryNorm(array $queries): float
    {
        $sum = 0;
        foreach ($queries as $query) {
            $docCount = $this->getDocCountForQuery($query);
            $sum += pow($this->calculateInverseDocumentFrequency($docCount), 2);
        }

        if ($sum === 0) {
            return 0;
        }

        return 1.0 / sqrt($sum);
    }

    public function joinTerm(string $field, string $term): string
    {
        $termName = $field . ucfirst($term) . 'Term';
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

    public function getDocCountForQuery(QueryBuilderInterface $query)
    {
        $queryBuilder = (new QueryBuilder($this->connection))
            ->select('count(document.id) as count')
            ->from($this->schema->getDocumentsTableName(), 'document');

        $expression = $query->build($queryBuilder->expr(), $queryBuilder);
        if ($expression) {
            $queryBuilder->where($expression);
        }

        return (int) $queryBuilder->execute()->fetchColumn();
    }

    public function getDocCountPerTerm(string $field, string $term)
    {
        $key = $field . '-' . $term;
        if (array_key_exists($key, $this->docCountPerTerm)) {
            return $this->docCountPerTerm[$key];
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

        return $this->docCountPerTerm[$key] = (int) $queryBuilder->execute()->fetchColumn();
    }
}
