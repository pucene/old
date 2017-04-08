<?php

namespace Pucene\Component\QueryBuilder\Sort;

interface SortInterface
{
    const ASC = 'asc';
    const DESC = 'desc';

    /**
     * Returns order.
     *
     * @return string
     */
    public function getOrder(): string;
}
