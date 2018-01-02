<?php

namespace Pucene\Component\Pucene\Dbal;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Pucene\Component\Pucene\Compiler\Compiler;
use Pucene\Component\Pucene\Dbal\Interpreter\DbalInterpreter;
use Pucene\Component\Pucene\Model\Analysis;
use Pucene\Component\Pucene\StorageInterface;
use Pucene\Component\Pucene\TermStatisticsInterface;
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

    public function __construct($name, Connection $connection, Compiler $compiler, DbalInterpreter $interpreter)
    {
        $this->name = $name;
        $this->connection = $connection;
        $this->interpreter = $interpreter;
        $this->compiler = $compiler;

        $this->persister = new DocumentPersister($connection, $this->getSchema());
    }

    public function exists(): bool
    {
        return $this->connection->getSchemaManager()->tablesExist([$this->getSchema()->getDocumentsTableName()]);
    }

    public function createIndex(array $parameters): void
    {
        $this->connection->beginTransaction();
        foreach ($this->getSchema()->toSql($this->connection->getDatabasePlatform()) as $sql) {
            $this->connection->exec($sql);
        }
        $this->connection->commit();
    }

    public function deleteIndex(): void
    {
        $this->connection->beginTransaction();
        foreach ($this->getSchema()->toDropSql($this->connection->getDatabasePlatform()) as $sql) {
            $this->connection->exec($sql);
        }
        $this->connection->commit();
    }

    public function saveDocument(Analysis $analysis): void
    {
        $this->connection->transactional(
            function() use ($analysis) {
                $this->persister->persist($analysis->getDocument(), $analysis->getFields());
            }
        );
    }

    public function deleteDocument(string $id): void
    {
        $this->connection->delete($this->getSchema()->getDocumentsTableName(), ['id' => $id]);
    }

    public function search(Search $search, array $types): array
    {
        $element = $this->compiler->compile($search->getQuery(), $this);
        if (null === $element) {
            return [];
        }

        return $this->interpreter->interpret($types, $search, $this, $element);
    }

    public function count(Search $search, array $types): int
    {
        $element = $this->compiler->compile($search->getQuery(), $this);
        if (null === $element) {
            return 0;
        }

        return $this->interpreter->count($types, $this, $element);
    }

    public function get(?string $type, string $id): array
    {
        $schema = $this->getSchema();
        $queryBuilder = (new QueryBuilder($this->connection))
            ->select('document')
            ->from($schema->getDocumentsTableName())
            ->andWhere('id = :id')
            ->setParameter('id', $id);

        if ($type) {
            $queryBuilder->andWhere('type = :type')
                ->setParameter('type', $type);
        }

        $document = json_decode($queryBuilder->execute()->fetchColumn(), true);
        if (!$document) {
            return [
                '_index' => $this->name,
                '_type' => $type,
                '_id' => $id,
                'found' => false,
            ];
        }

        return [
            '_index' => $this->name,
            '_type' => $type,
            '_id' => $id,
            '_source' => $document,
            'found' => true,
        ];
    }

    public function termStatistics(): TermStatisticsInterface
    {
        return new DbalTermStatistics($this->connection, $this->getSchema());
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    public function getSchema(): PuceneSchema
    {
        if ($this->schema) {
            return $this->schema;
        }

        return $this->schema = new PuceneSchema($this->name);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
