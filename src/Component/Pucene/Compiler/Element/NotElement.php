<?php

namespace Pucene\Component\Pucene\Compiler\Element;

use Pucene\Component\Pucene\Compiler\ElementInterface;

class NotElement extends BaseElement
{
    /**
     * @var ElementInterface
     */
    private $element;

    /**
     * @param ElementInterface $element
     * @param float $boost
     */
    public function __construct(ElementInterface $element, float $boost = 1)
    {
        parent::__construct($boost);

        $this->element = $element;
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
}
