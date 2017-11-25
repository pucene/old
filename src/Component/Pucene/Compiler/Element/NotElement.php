<?php

namespace Pucene\Component\Pucene\Compiler\Element;

use Pucene\Component\Pucene\Compiler\ElementInterface;

class NotElement extends BaseElement
{
    /**
     * @var ElementInterface
     */
    private $element;

    public function __construct(ElementInterface $element, float $boost = 1)
    {
        parent::__construct($boost);

        $this->element = $element;
    }

    public function getElement(): ElementInterface
    {
        return $this->element;
    }
}
