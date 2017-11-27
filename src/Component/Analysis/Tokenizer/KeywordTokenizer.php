<?php

namespace Pucene\Component\Analysis\Tokenizer;

use Pucene\Component\Analysis\Token;

class KeywordTokenizer implements TokenizerInterface
{
    public function tokenize(string $input): array
    {
        return [new Token($input, 0, strlen($input), '<ALPHANUM>', 1)];
    }
}
