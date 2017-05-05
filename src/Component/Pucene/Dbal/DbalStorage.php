<?php

namespace Pucene\Component\Pucene\Dbal;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Pucene\Component\Pucene\Compiler\Compiler;
use Pucene\Component\Pucene\Dbal\Interpreter\DbalInterpreter;
use Pucene\Component\Pucene\Dbal\QueryBuilder\ScoringQueryBuilder;
use Pucene\Component\Pucene\Model\Analysis;
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
     * @var Compiler
     */
    private $compiler;

    /**
     * @var DbalInterpreter
     */
    private $interpreter;

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
     * @param Compiler $compiler
     * @param DbalInterpreter $interpreter
     */
    public function __construct($name, Connection $connection, Compiler $compiler, DbalInterpreter $interpreter)
    {
        $this->name = $name;
        $this->connection = $connection;
        $this->interpreter = $interpreter;
        $this->compiler = $compiler;

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
            function(Connection $connection) use ($analysis) {
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

    /**
     * {@inheritdoc}
     */
    public function search(Search $search, $types)
    {
        $element = $this->compiler->compile($search->getQuery(), $this);

        return $this->interpreter->interpret($types, $search, $this, $element);
    }

    /**
     * {@inheritdoc}
     */
    public function get($type, $id)
    {
        $schema = $this->getSchema();
        $queryBuilder = (new QueryBuilder($this->connection))
            ->select('document')
            ->from($schema->getDocumentsTableName())
            ->where('type = :type')
            ->andWhere('id = :id')
            ->setParameter('type', $type)
            ->setParameter('id', $id);

        $document = json_decode($queryBuilder->execute()->fetchColumn(), true);

        return [
            '_index' => $this->name,
            '_type' => $type,
            '_id' => $id,
            '_source' => $document,
        ];
    }

    public function termStatistics()
    {
        return new DbalTermStatistics($this->connection, $this->getSchema());
    }

    public function createScoringQueryBuilder()
    {
        return new ScoringQueryBuilder($this->connection, $this->getSchema());
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function getSchema()
    {
        if ($this->schema) {
            return $this->schema;
        }

        return $this->schema = new PuceneSchema($this->name);
    }

    public function getName()
    {
        return $this->name;
    }
}
