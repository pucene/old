<?php

namespace Pucene\Component\Analysis\CharacterFilter;

class ChainCharacterFilter implements CharacterFilterInterface
{
    /**
     * @var CharacterFilterInterface[]
     */
    private $filters;

    /**
     * @param CharacterFilterInterface[] $filters
     */
    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * {@inheritdoc}
     */
    public function filter($input)
    {
        foreach ($this->filters as $filter) {
            $input = $filter->filter($input);
        }

        return $input;
    }
}
