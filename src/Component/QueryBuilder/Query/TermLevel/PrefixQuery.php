<?php

namespace Pucene\Component\QueryBuilder\Query\TermLevel;

use Pucene\Component\QueryBuilder\Query\QueryInterface;

class PrefixQuery implements QueryInterface
{
    const NAME = 'prefix';

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $prefix;

    public function __construct(string $field, string $prefix)
    {
        $this->field = $field;
        $this->prefix = $prefix;
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

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function setPrefix(string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }
}
