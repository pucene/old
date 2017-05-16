<?php

namespace Pucene\Component\Pucene\Dbal;

use Doctrine\DBAL\Connection;
use Pucene\Component\Analysis\Token;
use Pucene\Component\Pucene\Math\ElasticsearchPrecision;
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

        $documentTerms = [];
        foreach ($fields as $field) {
            $fieldTerms = [];
            foreach ($field->getTokens() as $token) {
                if (!array_key_exists($token->getEncodedTerm(), $fieldTerms)) {
                    $fieldTerms[$token->getEncodedTerm()] = 0;
                }
                if (!array_key_exists($token->getEncodedTerm(), $documentTerms)) {
                    $documentTerms[$token->getEncodedTerm()] = 0;
                }

                ++$fieldTerms[$token->getEncodedTerm()];
                ++$documentTerms[$token->getEncodedTerm()];

                $this->insertToken($document->getId(), $field->getName(), $token->getEncodedTerm(), $token);
            }

            $fieldNorm = ElasticsearchPrecision::fieldNorm($field->getNumberOfTerms());
            foreach ($fieldTerms as $term => $frequency) {
                $this->connection->update(
                    $this->schema->getTokensTableName(),
                    [
                        'term_frequency' => $frequency,
                        'field_norm' => $fieldNorm,
                    ],
                    ['document_id' => $document->getId(), 'field_name' => $field->getName(), 'term' => $term]
                );
            }
        }

        foreach ($documentTerms as $term => $frequency) {
            $this->connection->insert(
                $this->schema->getDocumentTermsTableName(),
                [
                    'document_id' => $document->getId(),
                    'term' => $term,
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

    /**
     * @param string $documentId
     * @param string $fieldName
     * @param int $termId
     * @param Token $token
     */
    protected function insertToken(string $documentId, string $fieldName, $termId, Token $token)
    {
        $this->connection->insert(
            $this->schema->getTokensTableName(),
            [
                'document_id' => $documentId,
                'field_name' => $fieldName,
                'term' => $termId,
                'start_offset' => $token->getStartOffset(),
                'end_offset' => $token->getEndOffset(),
                'position' => $token->getPosition(),
                'type' => $token->getType(),
            ]
        );
    }
}
