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
     * @param string $field
     * @param string $term
     * @param float $boost
     */
    public function __construct(string $field, string $term, float $boost = 1)
    {
        parent::__construct($boost);

        $this->field = $field;
        $this->term = $term;
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
}
