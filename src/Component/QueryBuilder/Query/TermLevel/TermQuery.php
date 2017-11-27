<?php

namespace Pucene\Component\QueryBuilder\Query\TermLevel;

use Pucene\Component\QueryBuilder\Query\QueryInterface;

class TermQuery implements QueryInterface
{
    const NAME = 'term';

    /**
     * @var string
     */
    private $field;

    /**
     * @var mixed
     */
    private $term;

    public function __construct(string $field, $term)
    {
        $this->field = $field;
        $this->term = $term;
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

    /**
     * @return mixed
     */
    public function getTerm()
    {
        return $this->term;
    }

    public function setTerm(string $term): self
    {
        $this->term = $term;

        return $this;
    }
}
