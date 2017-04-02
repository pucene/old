<?php

namespace Pucene\Component\Pucene\Dbal;

use Doctrine\DBAL\Connection;
use Pucene\Component\Pucene\Model\Analysis;
use Pucene\Component\Pucene\StorageInterface;

class DbalStorage implements StorageInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $name;

    /**
     * @var PuceneSchema
     */
    private $schema;

    /**
     * @var DocumentPersister
     */
    private $persister;

    /**
     * @param string $name
     * @param Connection $connection
     */
    public function __construct($name, Connection $connection)
    {
        $this->name = $name;
        $this->connection = $connection;

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

    private function getSchema()
    {
        if ($this->schema) {
            return $this->schema;
        }

        return $this->schema = new PuceneSchema($this->name);
    }
}
