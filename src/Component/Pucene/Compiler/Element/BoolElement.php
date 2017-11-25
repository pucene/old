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

    public function __construct(ElementInterface $element, array $scoringElements, float $boost = 1, bool $coord = true)
    {
        parent::__construct($boost);

        $this->element = $element;
        $this->scoringElements = $scoringElements;
        $this->coord = $coord;
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

    public function getCoord(): bool
    {
        return $this->coord;
    }
}
