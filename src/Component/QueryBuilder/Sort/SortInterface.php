<?php

namespace Pucene\Component\QueryBuilder\Sort;

interface SortInterface
{
    const ASC = 'asc';

    const DESC = 'desc';

    public function getField(): string;

    public function getOrder(): string;
}
