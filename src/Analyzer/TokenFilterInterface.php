<?php

namespace Pucene\Analyzer;

interface TokenFilterInterface
{
    /**
     * @param string[] $tokens
     *
     * @return string[]
     */
    public function filter(array $tokens): array;

}
