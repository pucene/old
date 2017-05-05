<?php

namespace Pucene\Component\Pucene\Dbal\Math;

use Doctrine\DBAL\Connection;
use Pucene\Component\Math\Expression\Value;
use Pucene\Component\Math\ExpressionInterface;
use Pucene\Component\Math\MathExpressionBuilder;
use Pucene\Component\Pucene\Compiler\ElementInterface;
use Pucene\Component\Pucene\Dbal\Interpreter\InterpreterInterface;
use Pucene\Component\Pucene\Dbal\Interpreter\PuceneQueryBuilder;
use Pucene\Component\Pucene\Dbal\PuceneSchema;
use Pucene\Component\Symfony\Pool\PoolInterface;

class Coord implements ExpressionInterface
{
    /**
     * @var ElementInterface[]
     */
    private $elements;

    /**
     * @var PoolInterface
     */
    private $interpreterPool;

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
     * @param ElementInterface[] $elements
     * @param PoolInterface $interpreterPool
     * @param PuceneSchema $schema
     * @param Connection $connection
     * @param MathExpressionBuilder $expr
     */
    public function __construct(
        array $elements,
        PoolInterface $interpreterPool,
        PuceneSchema $schema,
        Connection $connection,
        MathExpressionBuilder $expr
    ) {
        $this->elements = $elements;
        $this->interpreterPool = $interpreterPool;
        $this->schema = $schema;
        $this->connection = $connection;
        $this->expr = $expr;
    }

    public function __toString(): string
    {
        $sum = [];
        foreach ($this->elements as $element) {
            $queryBuilder = (new PuceneQueryBuilder($this->connection, $this->schema, 'innerDocument'))
                ->select('1')
                ->from($this->schema->getDocumentsTableName(), 'innerDocument')
                ->where('innerDocument.id = document.id')
                ->setMaxResults(1);

            /** @var InterpreterInterface $interpreter */
            $interpreter = $this->interpreterPool->get(get_class($element));

            $expression = $interpreter->interpret($element, $queryBuilder);
            if ($expression) {
                $queryBuilder->andWhere($expression);
            }

            $sum[] = $this->expr->coalesce($this->expr->variable($queryBuilder->getSQL()), $this->expr->value(0));
        }

        return $this->expr->devide(call_user_func_array([$this->expr, 'add'], $sum), new Value(count($this->elements)));
    }
}
