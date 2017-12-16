<?php

namespace Pucene\Component\Pucene\Dbal;

use Doctrine\DBAL\Connection;
use Pucene\Component\Mapping\Types;
use Pucene\Component\Pucene\Model\Document;
use Pucene\Component\Pucene\Model\Field;

class DocumentPersister
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var PuceneSchema
     */
    private $schema;

    public function __construct(Connection $connection, PuceneSchema $schema)
    {
        $this->connection = $connection;
        $this->schema = $schema;
    }

    /**
     * @param Field[] $fields
     */
    public function persist(Document $document, array $fields): void
    {
        $this->insertDocument($document);

        foreach ($fields as $field) {
            $this->insertField(
                $document->getId(),
                $field->getName(),
                $field->getNumberOfTerms()
            );

            $this->insertValue($document->getId(), $field);
            $this->insertTokens($document->getId(), $field);
        }
    }

    protected function insertDocument(Document $document): void
    {
        $this->connection->insert(
            $this->schema->getDocumentsTableName(),
            [
                'id' => $document->getId(),
                'type' => $document->getType(),
                'document' => json_encode($document->getDocument()),
                'indexed_at' => new \DateTime(),
            ],
            [
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                'datetime',
            ]
        );
    }

    protected function insertField(string $documentId, string $fieldName, int $fieldLength): void
    {
        $this->connection->insert(
            $this->schema->getFieldsTableName(),
            [
                'document_id' => $documentId,
                'field_name' => $fieldName,
                'field_length' => $fieldLength,
            ]
        );
    }

    protected function insertValue(string $documentId, Field $field): void
    {
        $value = $field->getValue();
        if (!is_array($value)) {
            $value = [$value];
        }

        foreach ($value as $item) {
            if (Types::DATE === $field->getType()) {
                $date = new \DateTime($item);
                $item = $date ? $date->format('Y-m-d H:i:s') : null;
            } elseif (Types::BOOLEAN === $field->getType()) {
                $item = $item ? 1 : 0;
            }

            $this->connection->insert(
                $this->schema->getFieldTableName($field->getType()),
                [
                    'document_id' => $documentId,
                    'field_name' => $field->getName(),
                    'value' => $item,
                ],
                [
                    \PDO::PARAM_STR,
                    \PDO::PARAM_STR,
                    \PDO::PARAM_STR,
                    $this->schema->getColumnType($field->getType()),
                ]
            );
        }
    }

    protected function insertTokens(string $documentId, Field $field): void
    {
        $fieldTerms = [];
        foreach ($field->getTokens() as $token) {
            if (!array_key_exists($token->getEncodedTerm(), $fieldTerms)) {
                $fieldTerms[$token->getEncodedTerm()] = 0;
            }

            ++$fieldTerms[$token->getEncodedTerm()];

            if ($fieldTerms[$token->getEncodedTerm()] > 1) {
                continue;
            }

            $this->insertTerm($token->getEncodedTerm());
            $this->insertToken(
                $documentId,
                $field->getName(),
                $token->getEncodedTerm(),
                $field->getNumberOfTerms()
            );
        }

        // update term frequency
        foreach ($fieldTerms as $term => $frequency) {
            $this->connection->update(
                $this->schema->getDocumentTermsTableName(),
                [
                    'term_frequency' => $frequency,
                ],
                [
                    'document_id' => $documentId,
                    'field_name' => $field->getName(),
                    'term' => $term,
                ]
            );
        }
    }

    protected function insertToken(string $documentId, string $fieldName, string $term, int $fieldLength): void
    {
        $this->connection->insert(
            $this->schema->getDocumentTermsTableName(),
            [
                'document_id' => $documentId,
                'field_name' => $fieldName,
                'term' => $term,
                'field_length' => $fieldLength,
            ]
        );
    }

    protected function insertTerm(string $term): void
    {
        $result = $this->connection->createQueryBuilder()
            ->select('term.term')
            ->from($this->schema->getTermsTableName(), 'term')
            ->where('term.term = :term')
            ->setParameter('term', $term)
            ->execute();

        if ($result->fetch()) {
            return;
        }

        $this->connection->insert(
            $this->schema->getTermsTableName(),
            [
                'term' => $term,
            ]
        );
    }
}
