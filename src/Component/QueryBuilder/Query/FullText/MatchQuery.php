<?php

namespace Pucene\Component\QueryBuilder\Query\FullText;

use Pucene\Component\QueryBuilder\Query\QueryInterface;

class MatchQuery implements QueryInterface
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $query;

    /**
     * @var int|string
     */
    private $fuzzy;

    /**
     * @param string $field
     * @param string $query
     */
    public function __construct(string $field, string $query)
    {
        $this->field = $field;
        $this->query = $query;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function setField($field): string
    {
        $this->field = $field;

        return $this;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function setQuery(string $query): self
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @return int|string
     */
    public function getFuzzy()
    {
        return $this->fuzzy;
    }

    /**
     * @param int|string $fuzzy
     */
    public function setFuzzy($fuzzy): self
    {
        $this->fuzzy = $fuzzy;

        return $this;
    }
}
