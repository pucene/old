<?php

namespace Pucene\Component\QueryBuilder;

use Pucene\Component\QueryBuilder\Query\QueryInterface;
use Pucene\Component\QueryBuilder\Sort\SortInterface;

class Search
{
    /**
     * To retrieve hits from a certain offset. Defaults to 0.
     *
     * @var int
     */
    private $from = 0;

    /**
     * The number of hits to return. Defaults to 10. If you do not care about getting some
     * hits back but only about the number of matches and/or aggregations, setting the value
     * to 0 will help performance.
     *
     * @var int
     */
    private $size = 10;

    /**
     * @var QueryInterface
     */
    private $query;

    /**
     * @var SortInterface[]
     */
    private $sorts = [];

    public function __construct(QueryInterface $query = null)
    {
        $this->query = $query;
    }

    public function getFrom(): int
    {
        return $this->from;
    }

    public function setFrom(int $from): self
    {
        $this->from = $from;

        return $this;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function setQuery(QueryInterface $query): self
    {
        $this->query = $query;

        return $this;
    }

    public function addSort(SortInterface $sort): self
    {
        $this->sorts[] = $sort;

        return $this;
    }

    /**
     * @return SortInterface[]
     */
    public function getSorts(): array
    {
        return $this->sorts;
    }

    public function getQuery(): QueryInterface
    {
        return $this->query;
    }
}
