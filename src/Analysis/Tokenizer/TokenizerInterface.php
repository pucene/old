<?php

namespace Pucene\Analysis\Tokenizer;

use Pucene\Analysis\Token;

interface TokenizerInterface
{
    /**
     * @param string $input
     *
     * @return Token[]
     */
    public function tokenize($input);
}
