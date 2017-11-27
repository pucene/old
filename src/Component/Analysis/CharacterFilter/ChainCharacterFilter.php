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

    public function filter(string $input): string
    {
        foreach ($this->filters as $filter) {
            $input = $filter->filter($input);
        }

        return $input;
    }
}
