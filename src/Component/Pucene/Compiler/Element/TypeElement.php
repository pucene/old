<?php

namespace Pucene\Component\Pucene\Compiler\Element;

class TypeElement extends BaseElement
{
    /**
     * @var string
     */
    private $type;

    public function __construct(string $type, float $boost = 1)
    {
        parent::__construct($boost);

        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
