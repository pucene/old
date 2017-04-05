<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Query\FullText;

use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Pucene\Component\Pucene\Dbal\QueryBuilder\ParameterBag;
use Pucene\Component\Pucene\Dbal\QueryBuilder\Query\TermLevel\TermQuery;
use Pucene\Component\Pucene\Dbal\QueryBuilder\QueryInterface;

/**
 * Represents match query.
 */
class MatchQuery implements QueryInterface
{
    /**
     * @var TermQuery[]
     */
    private $queries;

    /**
     * @param array $queries
     *
     * @internal param Match $query
     */
    public function __construct(array $queries)
    {
        $this->queries = $queries;
    }

    public function build(ExpressionBuilder $expr, ParameterBag $parameter)
    {
        $or = $expr->orX();
        foreach ($this->queries as $query) {
            $or->add($query->build($expr, $parameter));
        }

        return $or;
    }

    public function scoring()
    {
        // TODO: Implement scoring() method.
    }
}
