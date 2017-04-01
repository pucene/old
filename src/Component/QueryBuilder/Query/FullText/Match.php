<?php

namespace Pucene\Component\QueryBuilder\Query\FullText;

use Pucene\Component\QueryBuilder\Query\QueryInterface;

class Match implements QueryInterface
{
    const NAME = 'match';

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $query;

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
}
