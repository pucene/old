<?php

namespace Pucene\Component\Pucene\Compiler\Element;

class PrefixElement extends BaseElement
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $prefix;

    public function __construct(string $field, string $term, float $boost = 1)
    {
        parent::__construct($boost);

        $this->field = $field;
        $this->prefix = $term;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }
}
