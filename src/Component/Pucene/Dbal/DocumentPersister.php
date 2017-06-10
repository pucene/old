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
    public $connection;

    /**
     * @var PuceneSchema
     */
    public $schema;

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

                $this->insertToken(
                    $document->getId(),
                    $field->getName(),
                    $token->getEncodedTerm(),
                    ElasticsearchPrecision::fieldNorm($field->getNumberOfTerms())
                );

                $this->connection->createQueryBuilder()
                    ->update($this->schema->getDocumentTermsTableName())
                    ->set('frequency', 'frequency + 1')
                    ->andWhere('field_name = :fieldName')
                    ->andWhere('term = :term')
                    ->setParameter('fieldName', $field->getName())
                    ->setParameter('term', $token->getEncodedTerm())
                    ->execute();
            }

            // update term frequency
            foreach ($fieldTerms as $term => $frequency) {
                $this->connection->createQueryBuilder()
                    ->update($this->schema->getDocumentTermsTableName())
                    ->set('term_frequency', sqrt($frequency))
                    ->set('score', 'field_norm * ' . sqrt($frequency))
                    ->andWhere('document_ID = :document')
                    ->andWhere('field_name = :fieldName')
                    ->andWhere('term = :term')
                    ->setParameter('document', $document->getId())
                    ->setParameter('fieldName', $field->getName())
                    ->setParameter('term', $term)
                    ->execute();
            }
        }
    }

    public function optimize()
    {
        // TODO recalculate term frequency

        $docCount = $this->connection->createQueryBuilder()
            ->select('COUNT(id)')
            ->from($this->schema->getDocumentsTableName())
            ->execute()
            ->fetchColumn();

        // calculate inverse-document-frequency
        $this->connection->createQueryBuilder()
            ->update($this->schema->getDocumentTermsTableName())
            ->set('idf', '1 + log(' . $docCount . ' / (frequency + 1))')
            ->execute();
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
        $frequency = $this->connection->createQueryBuilder()
            ->select('frequency')
            ->from($this->schema->getDocumentTermsTableName())
            ->andWhere('field_name = :fieldName')
            ->andWhere('term = :term')
            ->setParameter('fieldName', $fieldName)
            ->setParameter('term', $term)
            ->execute()
            ->fetchColumn();

        $this->connection->insert(
            $this->schema->getDocumentTermsTableName(),
            [
                'document_id' => $documentId,
                'field_name' => $fieldName,
                'term' => $term,
                'field_norm' => $fieldNorm,
                'frequency' => $frequency ?: 0,
            ]
        );
    }
}
