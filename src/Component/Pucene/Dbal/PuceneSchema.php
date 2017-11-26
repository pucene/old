<?php

namespace Pucene\Component\Pucene\Dbal;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\Schema;
use Pucene\Component\Mapping\Types;

class PuceneSchema
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var Schema
     */
    private $schema;

    /**
     * @var string[]
     */
    private $tableNames;

    public function __construct(string $prefix)
    {
        $this->prefix = $prefix;
        $this->schema = new Schema();

        $this->createDocumentsTable();
        $this->createFieldsTable();
        $this->createTermsTable();
        $this->createDocumentTermsTable();
        $this->createDocumentFieldsTables();
    }

    private function createDocumentsTable(): void
    {
        $this->tableNames['documents'] = sprintf('pu_%s_documents', $this->prefix);

        $documents = $this->schema->createTable($this->tableNames['documents']);
        $documents->addColumn('id', 'string', ['length' => 255]);
        $documents->addColumn('number', 'integer', ['autoincrement' => true]);
        $documents->addColumn('type', 'string', ['length' => 255]);
        $documents->addColumn('document', 'json_array');
        $documents->addColumn('indexed_at', 'datetime');

        $documents->setPrimaryKey(['id']);
        $documents->addIndex(['type']);
        $documents->addUniqueIndex(['number']);
    }

    private function createFieldsTable(): void
    {
        $this->tableNames['fields'] = sprintf('pu_%s_fields', $this->prefix);

        $table = $this->schema->createTable($this->tableNames['fields']);
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('document_id', 'string', ['length' => 255]);
        $table->addColumn('field_name', 'string', ['length' => 255]);
        $table->addColumn('field_length', 'float', ['default' => 0]);

        $table->setPrimaryKey(['id']);
        $table->addForeignKeyConstraint(
            $this->tableNames['documents'],
            ['document_id'],
            ['id'],
            ['onDelete' => 'CASCADE']
        );
        $table->addIndex(['field_name']);
    }

    private function createDocumentTermsTable(): void
    {
        $this->tableNames['document_terms'] = sprintf('pu_%s_document_terms', $this->prefix);

        $fields = $this->schema->createTable($this->tableNames['document_terms']);
        $fields->addColumn('id', 'integer', ['autoincrement' => true]);
        $fields->addColumn('document_id', 'string', ['length' => 255]);
        $fields->addColumn('field_name', 'string', ['length' => 255]);
        $fields->addColumn('term', 'string', ['length' => 255]);
        $fields->addColumn('term_frequency', 'integer', ['default' => 0]);
        $fields->addColumn('field_length', 'float', ['default' => 0]);

        $fields->setPrimaryKey(['id']);
        $fields->addForeignKeyConstraint(
            $this->tableNames['documents'],
            ['document_id'],
            ['id'],
            ['onDelete' => 'CASCADE']
        );
        $fields->addForeignKeyConstraint($this->tableNames['terms'], ['term'], ['term']);
        $fields->addIndex(['field_name', 'term']);
    }

    private function createTermsTable(): void
    {
        $this->tableNames['terms'] = sprintf('pu_%s_terms', $this->prefix);

        $fields = $this->schema->createTable($this->tableNames['terms']);
        $fields->addColumn('term', 'string', ['length' => 255]);

        $fields->setPrimaryKey(['term']);
        $fields->addIndex(['term']);
    }

    /**
     * Create table per mapping type.
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-types.html
     */
    private function createDocumentFieldsTables(): void
    {
        // Text types
        $this->createDocumentFieldsTable(Types::TEXT, 'text');
        $this->createDocumentFieldsTable(Types::KEYWORD, 'text');

        // Numeric types
        $this->createDocumentFieldsTable(TYPES::FLOAT, 'float');
        $this->createDocumentFieldsTable(TYPES::INTEGER, 'bigint');

        // Date types
        $this->createDocumentFieldsTable(Types::DATE, 'datetime');

        // Boolean types
        $this->createDocumentFieldsTable(Types::BOOLEAN, 'boolean');
    }

    private function createDocumentFieldsTable($type, $columnType, $options = []): void
    {
        $tableName = sprintf('document_field_%ss', $type);
        $this->tableNames[$tableName] = sprintf('pu_%s_' . $tableName, $this->prefix);

        $fields = $this->schema->createTable($this->tableNames[$tableName]);
        $fields->addColumn('id', 'integer', ['autoincrement' => true]);
        $fields->addColumn('document_id', 'string', ['length' => 255]);
        $fields->addColumn('field_name', 'string', ['length' => 255]);
        $fields->addColumn(
            'value',
            $columnType,
            array_merge(
                ['notnull' => false],
                $options
            )
        );

        $fields->setPrimaryKey(['id']);
        $fields->addForeignKeyConstraint(
            $this->tableNames['documents'],
            ['document_id'],
            ['id'],
            ['onDelete' => 'CASCADE']
        );
        $fields->addIndex(['field_name']);

        // Text and blobs can't be indexed.
        if (!in_array($columnType, ['text', 'blob'])) {
            $fields->addIndex(['value']);
        }
    }

    public function toSql(AbstractPlatform $platform): array
    {
        return $this->schema->toSql($platform);
    }

    public function toDropSql(AbstractPlatform $platform): array
    {
        return $this->schema->toDropSql($platform);
    }

    public function getDocumentsTableName(): string
    {
        return $this->tableNames['documents'];
    }

    public function getFieldsTableName(): string
    {
        return $this->tableNames['fields'];
    }

    public function getDocumentTermsTableName(): string
    {
        return $this->tableNames['document_terms'];
    }

    public function getTermsTableName(): string
    {
        return $this->tableNames['terms'];
    }
}
