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

    public function __construct(Connection $connection, PuceneSchema $schema, string $documentAlias = 'document')
    {
        parent::__construct($connection);

        $this->documentAlias = $documentAlias;
        $this->schema = $schema;
    }

    public function math(): MathExpressionBuilder
    {
        return new MathExpressionBuilder();
    }

    public function joinTerm(string $field, string $term): string
    {
        $termName = $this->escape('term' . ucfirst($field) . ucfirst($term));
        if (in_array($termName, $this->joins)) {
            return $termName;
        }

        $condition = sprintf(
            '%1$s.document_id = %2$s.id AND %1$s.field_name = \'%3$s\' AND %1$s.term = \'%4$s\'',
            $termName,
            $this->documentAlias,
            $field,
            $term
        );

        $this->leftJoin($this->documentAlias, $this->schema->getDocumentTermsTableName(), $termName, $condition);

        return $this->joins[] = $termName;
    }

    private function escape($name): string
    {
        return trim(preg_replace('/\W/', '_', $name), '_');
    }
}
