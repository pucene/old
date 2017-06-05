<?php

namespace Pucene\Component\QueryBuilder;

use Pucene\Component\QueryBuilder\Query\QueryInterface;
use Pucene\Component\QueryBuilder\Sort\ScoreSort;
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

    /**
     * @param QueryInterface $query
     */
    public function __construct(QueryInterface $query = null)
    {
        $this->query = $query;

        $this->sorts = [new ScoreSort()];
    }

    /**
     * Returns from.
     *
     * @return int
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set from.
     *
     * @param int $from
     *
     * @return $this
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Returns sizer.
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set size.
     *
     * @param int $size
     *
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Set query.
     *
     * @param QueryInterface $query
     *
     * @return $this
     */
    public function setQuery(QueryInterface $query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Add sort.
     *
     * @param SortInterface $sort
     *
     * @return $this
     */
    public function addSort(SortInterface $sort)
    {
        $this->sorts[] = $sort;

        return $this;
    }

    /**
     * Returns sorts.
     *
     * @return SortInterface[]
     */
    public function getSorts(): array
    {
        return $this->sorts;
    }

    /**
     * Returns queries.
     *
     * @return QueryInterface
     */
    public function getQuery()
    {
        return $this->query;
    }
}
