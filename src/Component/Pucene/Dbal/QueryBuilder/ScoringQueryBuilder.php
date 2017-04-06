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
            ->setMaxResults(1);
    }

    public function inverseDocumentFrequency($term): float
    {
        return $this->calculateInverseDocumentFrequency($this->getDocCountPerTerm($term));
    }

    public function queryNorm(array $terms): float
    {
        $sum = 0;
        foreach ($this->getDocCountPerTerms($terms) as $term => $value) {
            $sum += pow($this->calculateInverseDocumentFrequency($value), 2);
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
            sprintf('innerdocument.id = %s.document_id and %s.name = \'%s\'', $fieldName, $fieldName, $field)
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

    private function getDocCountPerTerm($term)
    {
        if (array_key_exists($term, $this->docCountPerTerm)) {
            return $this->docCountPerTerm[$term];
        }

        $queryBuilder = (new QueryBuilder($this->connection))
            ->select('count(document.id) as count')
            ->from($this->schema->getDocumentsTableName(), 'document')
            ->innerJoin(
                'document',
                    $this->schema->getDocumentTermsTableName(),
                    'documentTerm',
                    'document.id = documentTerm.document_id and documentTerm.term = ?'
            )
            ->setParameter(0, $term);

        return $this->docCountPerTerm[$term] = (int) $queryBuilder->execute()->fetchColumn();
    }

    private function getDocCountPerTerms(array $terms)
    {
        $inExpression = $this->connection->getExpressionBuilder()->in(
            'documentTerm.term',
            "'" . implode("','", $terms) . "'"
        );

        $queryBuilder = (new QueryBuilder($this->connection))
            ->select('count(document.id) as count')
            ->addSelect('documentTerm.term as term')
            ->from($this->schema->getDocumentsTableName(), 'document')
            ->innerJoin(
                'document',
                $this->schema->getDocumentTermsTableName(),
                'documentTerm',
                'document.id = documentTerm.document_id and ' . $inExpression
            )
            ->groupBy('documentTerm.term');

        $result = [];
        foreach ($queryBuilder->execute() as $item) {
            $this->docCountPerTerm[$item['term']] = $result[$item['term']] = $item['count'];
        }

        return $result;
    }
}
