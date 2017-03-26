<?php

namespace Pucene\Analysis\TokenFilter;

use Pucene\Analysis\Token;

interface TokenFilterInterface
{
    /**
     * @param Token $token
     *
     * @return Token[]
     */
    public function filter(Token $token);
}
