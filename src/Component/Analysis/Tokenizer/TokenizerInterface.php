<?php

namespace Pucene\Component\Analysis\Tokenizer;

use Pucene\Component\Analysis\Token;

interface TokenizerInterface
{
    /**
     * @param string $input
     *
     * @return Token[]
     */
    public function tokenize($input);
}
