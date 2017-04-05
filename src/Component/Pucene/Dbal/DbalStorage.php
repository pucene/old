<?php

namespace Pucene\Component\Pucene\Dbal;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Pucene\Component\Pucene\Dbal\QueryBuilder\SearchBuilder;
use Pucene\Component\Pucene\Model\Analysis;
use Pucene\Component\Pucene\Model\Document;
use Pucene\Component\Pucene\StorageInterface;
use Pucene\Component\QueryBuilder\Search;

class DbalStorage implements StorageInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var SearchBuilder
     */
    private $searchBuilder;

    /**
     * @var DocumentPersister
     */
    private $persister;

    /**
     * @var PuceneSchema
     */
    private $schema;

    /**
     * @param string $name
     * @param Connection $connection
     * @param SearchBuilder $searchBuilder
     */
    public function __construct($name, Connection $connection, SearchBuilder $searchBuilder)
    {
        $this->name = $name;
        $this->connection = $connection;
        $this->searchBuilder = $searchBuilder;

        $this->persister = new DocumentPersister($connection, $this->getSchema());
    }

    /**
     * {@inheritdoc}
     */
    public function createIndex(array $parameters)
    {
        $this->connection->beginTransaction();
        foreach ($this->getSchema()->toSql($this->connection->getDatabasePlatform()) as $sql) {
            $this->connection->exec($sql);
        }
        $this->connection->commit();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteIndex()
    {
        $this->connection->beginTransaction();
        foreach ($this->getSchema()->toDropSql($this->connection->getDatabasePlatform()) as $sql) {
            $this->connection->exec($sql);
        }
        $this->connection->commit();
    }

    /**
     * {@inheritdoc}
     */
    public function saveDocument(Analysis $analysis)
    {
        $this->connection->transactional(
            function (Connection $connection) use ($analysis) {
                $this->persister->persist($analysis->getDocument(), $analysis->getFields());
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDocument($id)
    {
        $this->connection->delete($this->getSchema()->getDocumentsTableName(), ['id' => $id]);
    }

    public function search(Search $search, $type, $index)
    {
        $schema = $this->getSchema();

        $queryBuilder = (new QueryBuilder($this->connection))->from($schema->getDocumentsTableName(), 'document')
            ->select('document.*')
            ->innerJoin('document', $schema->getFieldsTableName(), 'field', 'field.document_id = document.id')
            ->innerJoin('field', $schema->getTokensTableName(), 'token', 'token.field_id = field.id')
            ->innerJoin('token', $schema->getTermsTableName(), 'term', 'token.term_id = term.id')
            ->where('document.type IN (?)')
            ->groupBy('document.id')
            ->setParameter(0, implode(',', $type));

        $this->searchBuilder->build($queryBuilder, $search);

        $result = $this->connection->fetchAll(
            $queryBuilder->getSQL(),
            $queryBuilder->getParameters()
        );

        return array_map(
            function ($row) use ($index) {
                return new Document($row['id'], $row['type'], $index, json_decode($row['document'], true));
            },
            $result
        );
    }

    private function getSchema()
    {
        if ($this->schema) {
            return $this->schema;
        }

        return $this->schema = new PuceneSchema($this->name);
    }
}
