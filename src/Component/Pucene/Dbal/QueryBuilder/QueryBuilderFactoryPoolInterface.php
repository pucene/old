<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder;

interface QueryBuilderFactoryPoolInterface
{
    public function get($className);
}
