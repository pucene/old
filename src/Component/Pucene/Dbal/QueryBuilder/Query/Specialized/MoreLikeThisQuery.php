<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Query\Specialized;

use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Pucene\Component\Pucene\Dbal\QueryBuilder\ParameterBag;
use Pucene\Component\Pucene\Dbal\QueryBuilder\Query\FullText\MatchQuery;
use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\DocumentLike;

class MoreLikeThisQuery extends MatchQuery
{
    /**
     * @var DocumentLike[]
     */
    private $exclude;

    /**
     * @param array $queries
     * @param DocumentLike[] $exclude
     */
    public function __construct(array $queries, array $exclude)
    {
        parent::__construct($queries);

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
