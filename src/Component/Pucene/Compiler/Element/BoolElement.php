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
     * @param ElementInterface $element
     * @param ElementInterface[] $scoringElements
     * @param float $boost
     */
    public function __construct(ElementInterface $element, array $scoringElements, float $boost = 1)
    {
        parent::__construct($boost);

        $this->element = $element;
        $this->scoringElements = $scoringElements;
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
}
