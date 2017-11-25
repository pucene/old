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

    public function __construct(string $field, string $term, float $boost = 1)
    {
        parent::__construct($boost);

        $this->field = $field;
        $this->term = $term;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getTerm(): string
    {
        return $this->term;
    }
}
