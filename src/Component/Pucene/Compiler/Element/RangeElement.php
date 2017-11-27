<?php

namespace Pucene\Component\Pucene\Compiler\Element;

class RangeElement extends BaseElement
{
    const OPERATOR_GTE = 'gte';
    const OPERATOR_GT = 'gt';
    const OPERATOR_LTE = 'lte';
    const OPERATOR_LT = 'lt';

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $operator;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct(string $field, string $operator, $value, float $boost = 1)
    {
        parent::__construct($boost);

        $this->field = $field;
        $this->operator = $operator;
        $this->value = $value;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
