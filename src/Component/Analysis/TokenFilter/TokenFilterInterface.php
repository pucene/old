<?php

namespace Pucene\Component\Analysis\TokenFilter;

use Pucene\Component\Analysis\Token;

interface TokenFilterInterface
{
    /**
     * @return Token[]
     */
    public function filter(Token $token): array;
}
