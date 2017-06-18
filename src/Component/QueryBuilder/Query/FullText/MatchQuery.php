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
    public function __construct($field, $query)
    {
        $this->field = $field;
        $this->query = $query;
    }

    /**
     * Returns field.
     *
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set field.
     *
     * @param string $field
     *
     * @return $this
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Returns query.
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set query.
     *
     * @param string $query
     *
     * @return $this
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Returns fuzzy.
     *
     * @return int|string
     */
    public function getFuzzy()
    {
        return $this->fuzzy;
    }

    /**
     * Set fuzzy.
     *
     * @param int|string $fuzzy
     *
     * @return $this
     */
    public function setFuzzy($fuzzy)
    {
        $this->fuzzy = $fuzzy;

        return $this;
    }
}
