<?php

namespace Pucene\Component\Pucene\Dbal;

use Doctrine\DBAL\Connection;
use Pucene\Component\Analysis\Token;
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

    /**
     * @param Connection $connection
     * @param PuceneSchema $schema
     */
    public function __construct(Connection $connection, PuceneSchema $schema)
    {
        $this->connection = $connection;
        $this->schema = $schema;
    }

    /**
     * @param Document $document
     * @param Field[] $fields
     */
    public function persist(Document $document, array $fields)
    {
        $this->insertDocument($document);

        $terms = [];
        $documentTerms = [];
        foreach ($fields as $field) {
            $fieldId = $this->insertField($document, $field);

            $fieldTerms = [];
            foreach ($field->getTokens() as $token) {
                if (!array_key_exists($token->getTerm(), $terms)) {
                    $terms[$token->getTerm()] = $this->findOrCreateTerm($token->getTerm());
                }

                if (!array_key_exists($token->getTerm(), $fieldTerms)) {
                    $fieldTerms[$token->getTerm()] = 0;
                }
                if (!array_key_exists($token->getTerm(), $documentTerms)) {
                    $documentTerms[$token->getTerm()] = 0;
                }

                $fieldTerms[$token->getTerm()]++;
                $documentTerms[$token->getTerm()]++;

                $this->insertToken($fieldId, $terms[$token->getTerm()], $token);
            }

            foreach ($fieldTerms as $term => $frequency) {
                $this->connection->insert(
                    $this->schema->getFieldTermsTableName(),
                    [
                        'field_id' => $fieldId,
                        'term_id' => $terms[$term],
                        'frequency' => $frequency,
                    ]
                );
            }
        }

        foreach ($documentTerms as $term => $frequency) {
            $this->connection->insert(
                $this->schema->getDocumentTermsTableName(),
                [
                    'document_id' => $document->getId(),
                    'term_id' => $terms[$term],
                    'frequency' => $frequency,
                ]
            );
        }
    }

    /**
     * @param Document $document
     */
    protected function insertDocument(Document $document)
    {
        $this->connection->insert(
            $this->schema->getDocumentsTableName(),
            [
                'id' => $document->getId(),
                'type' => $document->getType(),
                'document' => json_encode($document->getDocument()),
            ]
        );
    }

    /**
     * @param Document $document
     * @param Field $field
     *
     * @return string
     */
    protected function insertField(Document $document, Field $field)
    {
        $this->connection->insert(
            $this->schema->getFieldsTableName(),
            [
                'document_id' => $document->getId(),
                'name' => $field->getName(),
                'number_of_terms' => $field->getNumberOfTerms(),
            ]
        );
        $fieldId = $this->connection->lastInsertId();

        return $fieldId;
    }

    /**
     * @param string $term
     *
     * @return int
     */
    protected function findOrCreateTerm($term)
    {
        $result = $this->connection->fetchArray(
            'SELECT id FROM ' . $this->schema->getTermsTableName() . ' WHERE term = ?',
            [$term]
        );

        if ($result) {
            return $result[0];
        }

        $this->connection->insert($this->schema->getTermsTableName(), ['term' => $term]);

        return $this->connection->lastInsertId();
    }

    /**
     * @param int $fieldId
     * @param int $termId
     * @param Token $token
     */
    protected function insertToken($fieldId, $termId, Token $token)
    {
        $this->connection->insert(
            $this->schema->getTokensTableName(),
            [
                'field_id' => $fieldId,
                'term_id' => $termId,
                'start_offset' => $token->getStartOffset(),
                'end_offset' => $token->getEndOffset(),
                'position' => $token->getPosition(),
                'type' => $token->getType(),
            ]
        );
    }
}
