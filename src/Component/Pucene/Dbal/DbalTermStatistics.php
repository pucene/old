<?php

namespace Pucene\Component\Pucene\Dbal;

use Doctrine\DBAL\Connection;
use Pucene\Component\Pucene\Dbal\Interpreter\PuceneQueryBuilder;
use Pucene\Component\Pucene\TermStatisticsInterface;

class DbalTermStatistics implements TermStatisticsInterface
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
     * @var int
     */
    private $docCount;

    public function __construct(Connection $connection, PuceneSchema $schema)
    {
        $this->connection = $connection;
        $this->schema = $schema;
    }

    public function documentCount(string $field, string $term): int
    {
        $queryBuilder = (new PuceneQueryBuilder($this->connection, $this->schema))
            ->select('count(document.id) as count')
            ->from($this->schema->getDocumentsTableName(), 'document');

        $queryBuilder->where($queryBuilder->expr()->isNotNull($queryBuilder->joinTerm($field, $term) . '.id'));

        return (int) $queryBuilder->execute()->fetchColumn();
    }

    /**
     * {@inheritdoc}
     */
    public function inverseDocumentFrequency(string $field, string $term): float
    {
        return $this->calculateInverseDocumentFrequency($this->documentCount($field, $term));
    }

    private function calculateInverseDocumentFrequency($docCount): float
    {
        return 1 + log((float) $this->getDocCount() / ($docCount + 1));
    }

    private function getDocCount(): int
    {
        if ($this->docCount) {
            return $this->docCount;
        }

        $queryBuilder = (new PuceneQueryBuilder($this->connection, $this->schema))
            ->select('count(document.id) as count')
            ->from($this->schema->getDocumentsTableName(), 'document');

        return $this->docCount = (int) $queryBuilder->execute()->fetchColumn();
    }
}
