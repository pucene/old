<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder;

interface QueryBuilderPoolInterface
{
    public function get($className);
}
