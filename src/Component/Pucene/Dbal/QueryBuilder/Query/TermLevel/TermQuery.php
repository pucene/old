<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Query\TermLevel;

use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Pucene\Component\Pucene\Dbal\QueryBuilder\ParameterBag;
use Pucene\Component\Pucene\Dbal\QueryBuilder\QueryInterface;
use Pucene\Component\QueryBuilder\Query\TermLevel\Term;

/**
 * Represents term query.
 */
class TermQuery implements QueryInterface
{
    /**
     * @var Term
     */
    private $query;

    /**
     * @param Term $query
     */
    public function __construct(Term $query)
    {
        $this->query = $query;
    }

    public function build(ExpressionBuilder $expr, ParameterBag $parameter)
    {
        $parameter->add($this->query->getField());
        $parameter->add($this->query->getTerm());

        return $expr->andX(
            $expr->eq('field.name', '?'),
            $expr->eq('term.term', '?')
        );
    }

    public function scoring()
    {
        // TODO: Implement scoring() method.
    }
}
