<?php

namespace Pucene\Component\Pucene\Compiler\Element;

class TypeElement extends BaseElement
{
    /**
     * @var string
     */
    private $type;

    /**
     * @param string $type
     * @param float $boost
     */
    public function __construct($type, float $boost = 1)
    {
        parent::__construct($boost);

        $this->type = $type;
    }

    /**
     * Returns type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
