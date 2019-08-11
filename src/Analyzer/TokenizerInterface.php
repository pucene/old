<?php

namespace Pucene\Analyzer;

interface TokenizerInterface
{
    /**
     * @return string[]
     */
    public function tokenize(string $sentence): array;
}
