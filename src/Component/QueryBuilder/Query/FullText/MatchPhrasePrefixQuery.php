<?php

namespace Pucene\Component\QueryBuilder\Query\FullText;

use Pucene\Component\QueryBuilder\Query\QueryInterface;

class MatchPhrasePrefixQuery implements QueryInterface
{
    const NAME = 'match_phrase_prefix';

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $phrase;

    public function __construct(string $field, string $phrase)
    {
        $this->field = $field;
        $this->phrase = $phrase;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function setField(string $field): self
    {
        $this->field = $field;

        return $this;
    }

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function setPhrase(string $phrase): self
    {
        $this->phrase = $phrase;

        return $this;
    }
}
