<?php

namespace Pucene\Component\QueryBuilder\Query\TermLevel;

use Pucene\Component\QueryBuilder\Query\QueryInterface;

class Term implements QueryInterface
{
    const NAME = 'term';

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $term;

    /**
     * @param string $field
     * @param string $term
     */
    public function __construct($field, $term)
    {
        $this->field = $field;
        $this->term = $term;
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
     * Returns term.
     *
     * @return string
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * Set term.
     *
     * @param string $term
     *
     * @return $this
     */
    public function setTerm($term)
    {
        $this->term = $term;

        return $this;
    }
}
