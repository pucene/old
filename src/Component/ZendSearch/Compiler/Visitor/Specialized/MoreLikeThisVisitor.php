<?php

namespace Pucene\Component\ZendSearch\Compiler\Visitor\Specialized;

use Pucene\Component\QueryBuilder\Query\QueryInterface;
use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\MoreLikeThisQuery;
use Pucene\Component\ZendSearch\Compiler\VisitorInterface;

class MoreLikeThisVisitor implements VisitorInterface
{
    /**
     * {@inheritdoc}
     *
     * @param MoreLikeThisQuery $query
     */
    public function visit(QueryInterface $query)
    {
    }
}
