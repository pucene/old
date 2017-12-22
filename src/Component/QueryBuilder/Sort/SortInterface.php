<?php

namespace Pucene\Component\QueryBuilder\Sort;

interface SortInterface
{
    const ASC = 'asc';

    const DESC = 'desc';

    public function getOrder(): string;
}
