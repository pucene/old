<?php

namespace Pucene\Analyzer;

interface CharacterFilterInterface
{
    public function filter(string $sentence): string;
}
