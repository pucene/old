<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Query\Specialized;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Pucene\Component\Pucene\Dbal\PuceneSchema;
use Pucene\Component\Pucene\Dbal\QueryBuilder\ParameterBag;
use Pucene\Component\Pucene\Dbal\QueryBuilder\Query\Compound\BoolBuilder;
use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\DocumentLike;

class MoreLikeThisBuilder extends BoolBuilder
{
    /**
     * @var DocumentLike[]
     */
    private $exclude;

    /**
     * @param array $queries
     * @param DocumentLike[] $exclude
     * @param PuceneSchema $schema
     * @param Connection $connection
     */
    public function __construct(array $queries, array $exclude, PuceneSchema $schema, Connection $connection)
    {
        parent::__construct($queries, [], [], [], $schema, $connection);

        $this->exclude = array_filter(
            $exclude,
            function ($like) {
                return $like instanceof DocumentLike;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function build(ExpressionBuilder $expr, ParameterBag $parameter)
    {
        $expression = $expr->andX(parent::build($expr, $parameter));
        if (0 === count($this->exclude)) {
            return $expression;
        }

        foreach ($this->exclude as $document) {
            $expression->add(
                'NOT' . $expr->andX(
                    $expr->eq('document.id', "'" . $document->getId() . "'"),
                    $expr->eq('document.type', "'" . $document->getType() . "'")
                )
            );
        }

        return $expression;
    }
}
