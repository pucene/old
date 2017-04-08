<?php

namespace Pucene\Component\Pucene\Dbal;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\Schema;

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

    public function __construct($prefix)
    {
        $this->prefix = $prefix;
        $this->schema = new Schema();

        $this->createDocumentsTable();
        $this->createFieldsTable();
        $this->createTermsTable();
        $this->createTokensTable();
        $this->createDocumentTermsTable();
        $this->createFieldTermsTable();
    }

    private function createDocumentsTable()
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

    private function createFieldsTable()
    {
        $this->tableNames['fields'] = sprintf('pu_%s_fields', $this->prefix);

        $fields = $this->schema->createTable($this->tableNames['fields']);
        $fields->addColumn('id', 'integer', ['autoincrement' => true]);
        $fields->addColumn('document_id', 'string', ['length' => 255]);
        $fields->addColumn('name', 'string', ['length' => 255]);
        $fields->addColumn('number_of_terms', 'integer');
        $fields->addColumn('field_norm', 'float');
        $fields->setPrimaryKey(['id']);
        $fields->addForeignKeyConstraint(
            $this->tableNames['documents'],
            ['document_id'],
            ['id'],
            ['onDelete' => 'CASCADE']
        );
    }

    private function createTokensTable()
    {
        $this->tableNames['tokens'] = sprintf('pu_%s_tokens', $this->prefix);

        $fields = $this->schema->createTable($this->tableNames['tokens']);
        $fields->addColumn('id', 'integer', ['autoincrement' => true]);
        $fields->addColumn('field_id', 'integer');
        $fields->addColumn('term', 'string', ['length' => 255]);
        $fields->addColumn('start_offset', 'integer');
        $fields->addColumn('end_offset', 'integer');
        $fields->addColumn('position', 'integer');
        $fields->addColumn('type', 'string', ['length' => 255]);
        $fields->setPrimaryKey(['id']);
        $fields->addForeignKeyConstraint($this->tableNames['fields'], ['field_id'], ['id'], ['onDelete' => 'CASCADE']);
        $fields->addForeignKeyConstraint($this->tableNames['terms'], ['term'], ['term']);
    }

    private function createTermsTable()
    {
        $this->tableNames['terms'] = sprintf('pu_%s_terms', $this->prefix);

        $fields = $this->schema->createTable($this->tableNames['terms']);
        $fields->addColumn('term', 'string', ['length' => 255]);
        $fields->setPrimaryKey(['term']);
    }

    private function createDocumentTermsTable()
    {
        $this->tableNames['document_terms'] = sprintf('pu_%s_document_terms', $this->prefix);

        $fields = $this->schema->createTable($this->tableNames['document_terms']);
        $fields->addColumn('id', 'integer', ['autoincrement' => true]);
        $fields->addColumn('document_id', 'string', ['length' => 255]);
        $fields->addColumn('term', 'string', ['length' => 255]);
        $fields->addColumn('frequency', 'integer');
        $fields->setPrimaryKey(['id']);
        $fields->addForeignKeyConstraint(
            $this->tableNames['documents'],
            ['document_id'],
            ['id'],
            ['onDelete' => 'CASCADE']
        );
        $fields->addForeignKeyConstraint($this->tableNames['terms'], ['term'], ['term']);
    }

    private function createFieldTermsTable()
    {
        $this->tableNames['field_terms'] = sprintf('pu_%s_field_terms', $this->prefix);

        $fields = $this->schema->createTable($this->tableNames['field_terms']);
        $fields->addColumn('id', 'integer', ['autoincrement' => true]);
        $fields->addColumn('field_id', 'integer');
        $fields->addColumn('term', 'string', ['length' => 255]);
        $fields->addColumn('frequency', 'integer');
        $fields->setPrimaryKey(['id']);
        $fields->addForeignKeyConstraint($this->tableNames['fields'], ['field_id'], ['id'], ['onDelete' => 'CASCADE']);
        $fields->addForeignKeyConstraint($this->tableNames['terms'], ['term'], ['term']);
    }

    public function toSql(AbstractPlatform $platform)
    {
        return $this->schema->toSql($platform);
    }

    public function toDropSql(AbstractPlatform $platform)
    {
        return $this->schema->toDropSql($platform);
    }

    public function getDocumentsTableName()
    {
        return $this->tableNames['documents'];
    }

    public function getFieldsTableName()
    {
        return $this->tableNames['fields'];
    }

    public function getTokensTableName()
    {
        return $this->tableNames['tokens'];
    }

    public function getTermsTableName()
    {
        return $this->tableNames['terms'];
    }

    public function getDocumentTermsTableName()
    {
        return $this->tableNames['document_terms'];
    }

    public function getFieldTermsTableName()
    {
        return $this->tableNames['field_terms'];
    }
}
