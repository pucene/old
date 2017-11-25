<?php

namespace Pucene\Component\Analysis\Tokenizer;

use Pucene\Component\Analysis\Token;

interface TokenizerInterface
{
    /**
     * @return Token[]
     */
    public function tokenize(string $input): array;
}
