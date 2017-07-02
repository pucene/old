<?php

namespace Pucene\Component\Pucene\Dbal;

use Doctrine\DBAL\Connection;
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

        foreach ($fields as $field) {
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
                    $document->getId(),
                    $field->getName(),
                    $token->getEncodedTerm(),
                    ElasticsearchPrecision::fieldNorm($field->getNumberOfTerms())
                );
            }

            // update term frequency
            foreach ($fieldTerms as $term => $frequency) {
                $this->connection->update(
                    $this->schema->getDocumentTermsTableName(),
                    [
                        'term_frequency' => $frequency,
                    ],
                    ['document_id' => $document->getId(), 'field_name' => $field->getName(), 'term' => $term]
                );
            }
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
     * @param string $term
     * @param float $fieldNorm
     */
    protected function insertToken(string $documentId, string $fieldName, $term, $fieldNorm)
    {
        $this->connection->insert(
            $this->schema->getDocumentTermsTableName(),
            [
                'document_id' => $documentId,
                'field_name' => $fieldName,
                'term' => $term,
                'field_norm' => $fieldNorm,
            ]
        );
    }

    protected function insertTerm(string $term)
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
