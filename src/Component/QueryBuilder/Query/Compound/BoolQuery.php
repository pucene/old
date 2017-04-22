<?php

namespace Pucene\Component\QueryBuilder\Query\Compound;

use Pucene\Component\QueryBuilder\Query\QueryInterface;

class BoolQuery implements QueryInterface
{
    /**
     * @var QueryInterface[]
     */
    private $shouldQueries;

    public function should(QueryInterface $query): self
    {
        $this->shouldQueries[] = $query;

        return $this;
    }

    /**
     * Returns shouldQueries.
     *
     * @return QueryInterface[]
     */
    public function getShouldQueries(): array
    {
        return $this->shouldQueries;
    }
}
