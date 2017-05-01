<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Math;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Pucene\Component\Math\Expression\Value;
use Pucene\Component\Math\ExpressionInterface;
use Pucene\Component\Math\MathExpressionBuilder;
use Pucene\Component\Pucene\Dbal\PuceneSchema;
use Pucene\Component\Pucene\Dbal\QueryBuilder\QueryBuilderInterface;

class Coord implements ExpressionInterface
{
    /**
     * @var QueryBuilderInterface[]
     */
    private $queries;

    /**
     * @var MathExpressionBuilder
     */
    private $expr;

    /**
     * @var PuceneSchema
     */
    private $schema;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param QueryBuilderInterface[] $queries
     * @param PuceneSchema $schema
     * @param Connection $connection
     * @param MathExpressionBuilder $expr
     */
    public function __construct(
        array $queries,
        PuceneSchema $schema,
        Connection $connection,
        MathExpressionBuilder $expr
    ) {
        $this->queries = $queries;
        $this->schema = $schema;
        $this->connection = $connection;
        $this->expr = $expr;
    }

    public function __toString(): string
    {
        $sum = [];
        foreach ($this->queries as $query) {
            $queryBuilder = (new QueryBuilder($this->connection))->select('1')
                ->from(
                    $this->schema->getDocumentsTableName(),
                    'document'
                )
                ->innerJoin(
                    'document',
                    $this->schema->getFieldsTableName(),
                    'field',
                    'field.document_id = document.id'
                )
                ->innerJoin('field', $this->schema->getTokensTableName(), 'token', 'token.field_id = field.id')
                ->where('innerDocument.id = document.id')
                ->setMaxResults(1);

            $expression = $query->build($queryBuilder->expr(), $queryBuilder);
            if ($expression) {
                $queryBuilder->andWhere($expression);
            }

            $sum[] = $this->expr->coalesce($this->expr->variable($queryBuilder->getSQL()), $this->expr->value(0));
        }

        return $this->expr->devide(call_user_func_array([$this->expr, 'add'], $sum), new Value(count($this->queries)));
    }
}
