<?php

namespace Pucene\Component\Pucene\Compiler\Element;

class MatchPhrasePrefixElement extends BaseElement
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $phrase;

    public function __construct(string $field, string $phrase, float $boost = 1)
    {
        parent::__construct($boost);

        $this->field = $field;
        $this->phrase = $phrase;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getPhrase(): string
    {
        return $this->phrase;
    }
}
