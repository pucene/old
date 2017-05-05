<?php

namespace Pucene\Component\Pucene\Compiler\Element;

class IdsElement extends BaseElement
{
    /**
     * @var string[]
     */
    private $ids;

    /**
     * @param string[] $ids
     * @param float $boost
     */
    public function __construct(array $ids, float $boost = 1)
    {
        parent::__construct($boost);

        $this->ids = $ids;
    }

    /**
     * Returns ids.
     *
     * @return \string[]
     */
    public function getIds(): array
    {
        return $this->ids;
    }
}
