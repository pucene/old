<?php

namespace Pucene\Component\Analysis\CharacterFilter;

interface CharacterFilterInterface
{
    /**
     * @param string $input
     *
     * @return string
     */
    public function filter($input);
}
