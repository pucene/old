<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder;

use Doctrine\DBAL\Query\Expression\ExpressionBuilder;

interface QueryInterface
{
    /**
     * @param ExpressionBuilder $expr
     * @param ParameterBag $parameter
     *
     * @return mixed
     */
    public function build(ExpressionBuilder $expr, ParameterBag $parameter);

    public function scoring();
}
