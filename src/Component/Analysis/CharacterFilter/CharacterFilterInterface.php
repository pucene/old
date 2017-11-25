<?php

namespace Pucene\Component\Analysis\CharacterFilter;

interface CharacterFilterInterface
{
    public function filter(string $input): string;
}
