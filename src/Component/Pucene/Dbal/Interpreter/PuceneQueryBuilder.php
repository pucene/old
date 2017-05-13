<?php

namespace Pucene\Component\Pucene\Dbal\Interpreter;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Pucene\Component\Math\MathExpressionBuilder;
use Pucene\Component\Pucene\Dbal\PuceneSchema;

class PuceneQueryBuilder extends QueryBuilder
{
    /**
     * @var string
     */
    private $documentAlias;

    /**
     * @var PuceneSchema
     */
    private $schema;

    /**
     * @var string[]
     */
    private $joins = [];

    /**
     * @param Connection $connection
     * @param PuceneSchema $schema
     * @param string $documentAlias
     */
    public function __construct(Connection $connection, PuceneSchema $schema, string $documentAlias = 'document')
    {
        parent::__construct($connection);

        $this->documentAlias = $documentAlias;
        $this->schema = $schema;
    }

    public function math()
    {
        return new MathExpressionBuilder();
    }

    public function selectFrequency(string $field, string $term)
    {
        $frequencyName = $this->escape('frequency' . ucfirst($field) . ucfirst($term));
        if (in_array($frequencyName, $this->joins)) {
            return $frequencyName . '.frequency';
        }

        $fieldName = $this->joinField($field);
        $this->leftJoin(
            $fieldName,
            $this->schema->getFieldTermsTableName(),
            $frequencyName,
            $frequencyName . '.field_id = ' . $fieldName . '.id AND ' . $frequencyName . '.term = \'' . $term . '\''
        );

        $this->joins[] = $frequencyName;

        return $frequencyName . '.frequency';
    }

    public function joinTerm(string $field, string $term)
    {
        $termName = $this->escape('term' . ucfirst($field) . ucfirst($term));
        if (in_array($termName, $this->joins)) {
            return $termName;
        }

        $fieldName = $this->joinField($field);
        $this->leftJoin(
            $fieldName,
            $this->schema->getTokensTableName(),
            $termName,
            $termName . '.field_id = ' . $fieldName . '.id AND ' . $termName . '.term = \'' . $term . '\''
        );

        return $this->joins[] = $termName;
    }

    public function joinField(string $field)
    {
        $fieldName = $this->escape('field' . ucfirst($field));
        if (in_array($fieldName, $this->joins)) {
            return $fieldName;
        }

        $this->leftJoin(
            $this->documentAlias,
            $this->schema->getFieldsTableName(),
            $fieldName,
            $fieldName . '.document_id = ' . $this->documentAlias . '.id AND ' . $fieldName . '.name = \'' . $field . '\''
        );

        return $this->joins[] = $fieldName;
    }

    private function escape($name)
    {
        return trim(preg_replace('/\W/', '_', $name), '_');
    }
}
