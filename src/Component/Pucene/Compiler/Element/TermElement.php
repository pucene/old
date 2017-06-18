<?php

namespace Pucene\Component\Pucene\Compiler\Element;

class TermElement extends BaseElement
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $term;

    /**
     * @var int|string
     */
    private $fuzzy;

    /**
     * @param string $field
     * @param string $term
     * @param float $boost
     * @param int|string $fuzzy
     */
    public function __construct(string $field, string $term, float $boost = 1, $fuzzy = null)
    {
        parent::__construct($boost);

        $this->field = $field;
        $this->term = $term;
        $this->fuzzy = $fuzzy;
    }

    /**
     * Returns field.
     *
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * Returns term.
     *
     * @return string
     */
    public function getTerm(): string
    {
        return $this->term;
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
}
