<?php

namespace Pucene\Component\QueryBuilder\Query\TermLevel;

use Pucene\Component\QueryBuilder\Query\QueryInterface;

class RangeQuery implements QueryInterface
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var mixed
     */
    private $gte;

    /**
     * @var mixed
     */
    private $gt;

    /**
     * @var mixed
     */
    private $lte;

    /**
     * @var mixed
     */
    private $lt;

    public function __construct(string $field)
    {
        $this->field = $field;
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
     * @param mixed $value
     */
    public function gte($value): self
    {
        $this->gte = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGte()
    {
        return $this->gte;
    }

    /**
     * @param mixed $value
     */
    public function gt($value): self
    {
        $this->gt = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGt()
    {
        return $this->gt;
    }

    /**
     * @param mixed $value
     */
    public function lte($value): self
    {
        $this->lte = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLte()
    {
        return $this->lte;
    }

    /**
     * @param mixed $value
     */
    public function lt($value): self
    {
        $this->lt = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLt()
    {
        return $this->lt;
    }
}
