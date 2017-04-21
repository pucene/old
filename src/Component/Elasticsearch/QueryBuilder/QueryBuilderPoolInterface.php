<?php

namespace Pucene\Component\Elasticsearch\QueryBuilder;

interface QueryBuilderPoolInterface
{
    public function get($className);
}
