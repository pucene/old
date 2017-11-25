<?php

namespace Pucene\Component\Analysis\CharacterFilter;

class StandardCharacterFilter implements CharacterFilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function filter(string $input): string
    {
        return $input;
    }
}
