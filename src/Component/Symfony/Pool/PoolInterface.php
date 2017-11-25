<?php

namespace Pucene\Component\Symfony\Pool;

interface PoolInterface
{
    public function get(string $alias);
}
