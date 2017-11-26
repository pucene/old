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

    public function __construct(ElementInterface $element, array $scoringElements, float $boost = 1)
    {
        parent::__construct($boost);

        $this->element = $element;
        $this->scoringElements = $scoringElements;
    }

    public function getElement(): ElementInterface
    {
        return $this->element;
    }

    /**
     * @return ElementInterface[]
     */
    public function getScoringElements(): array
    {
        return $this->scoringElements;
    }
}
