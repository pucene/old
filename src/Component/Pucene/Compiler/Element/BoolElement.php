<?php

namespace Pucene\Component\Pucene\Compiler\Element;

use Pucene\Component\Pucene\Compiler\ElementInterface;

class BoolElement extends BaseElement
{
    /**
     * @var ElementInterface
     */
    private $element;

    /**
     * @var ElementInterface[]
     */
    private $scoringElements;

    /**
     * @var bool
     */
    private $coord;

    /**
     * @param ElementInterface $element
     * @param ElementInterface[] $scoringElements
     * @param float $boost
     * @param bool $coord
     */
    public function __construct(ElementInterface $element, array $scoringElements, float $boost = 1, bool $coord = true)
    {
        parent::__construct($boost);

        $this->element = $element;
        $this->scoringElements = $scoringElements;
        $this->coord = $coord;
    }

    /**
     * Returns element.
     *
     * @return ElementInterface
     */
    public function getElement(): ElementInterface
    {
        return $this->element;
    }

    /**
     * Returns scoringElements.
     *
     * @return ElementInterface[]
     */
    public function getScoringElements(): array
    {
        return $this->scoringElements;
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
