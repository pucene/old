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
     * @var bool
     */
    private $coord;

    /**
     * @param string $operator
     * @param ElementInterface[] $elements
     * @param float $boost
     * @param bool $coord
     */
    public function __construct(string $operator, array $elements = [], float $boost = 1, bool $coord = true)
    {
        parent::__construct($boost);

        $this->operator = $operator;
        $this->elements = $elements;
        $this->coord = $coord;
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

    /**
     * Returns coord.
     *
     * @return bool
     */
    public function getCoord(): bool
    {
        return $this->coord;
    }
}
