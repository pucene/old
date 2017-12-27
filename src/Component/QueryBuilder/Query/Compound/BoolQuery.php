<?php

namespace Pucene\Component\QueryBuilder\Query\Compound;

use Pucene\Component\QueryBuilder\Query\QueryInterface;

class BoolQuery implements QueryInterface
{
    /**
     * @var QueryInterface[]
     */
    private $shouldQueries = [];

    /**
     * @var QueryInterface[]
     */
    private $mustQueries = [];

    /**
     * @var QueryInterface[]
     */
    private $mustNotQueries = [];

    /**
     * @var QueryInterface[]
     */
    private $filterQueries = [];

    public function should(QueryInterface $query): self
    {
        $this->shouldQueries[] = $query;

        return $this;
    }

    public function must(QueryInterface $query): self
    {
        $this->mustQueries[] = $query;

        return $this;
    }

    public function mustNot(QueryInterface $query): self
    {
        $this->mustNotQueries[] = $query;

        return $this;
    }

    public function filter(QueryInterface $query): self
    {
        $this->filterQueries[] = $query;

        return $this;
    }

    /**
     * @return QueryInterface[]
     */
    public function getShouldQueries(): array
    {
        return $this->shouldQueries;
    }

    /**
     * @return QueryInterface[]
     */
    public function getMustQueries(): array
    {
        return $this->mustQueries;
    }

    /**
     * @return QueryInterface[]
     */
    public function getMustNotQueries(): array
    {
        return $this->mustNotQueries;
    }

    /**
     * @return QueryInterface[]
     */
    public function getFilterQueries(): array
    {
        return $this->filterQueries;
    }

    public function isEmpty(): bool
    {
        return empty($this->getShouldQueries())
            || empty($this->getMustNotQueries())
            || empty($this->getMustQueries())
            || empty($this->getFilterQueries());
    }
}
