<?php

namespace Pucene\Component\Pucene\Compiler\Element;

use Pucene\Component\Pucene\Compiler\ElementInterface;

class CompositeElement extends BaseElement
{
    const OR = 'or';
    const AND = 'and';

    /**
     * @var string
     */
    private $operator;

    /**
     * @var ElementInterface[]
     */
    private $elements = [];

    /**
     * @param string $operator
     * @param ElementInterface[] $elements
     * @param float $boost
     */
    public function __construct(string $operator, array $elements = [], float $boost = 1)
    {
        parent::__construct($boost);

        $this->operator = $operator;
        $this->elements = $elements;
    }

    /**
     * Returns operator.
     *
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * Returns elements.
     *
     * @return ElementInterface[]
     */
    public function getElements(): array
    {
        return $this->elements;
    }
}
