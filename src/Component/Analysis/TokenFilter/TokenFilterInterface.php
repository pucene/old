<?php

namespace Pucene\Component\Analysis\TokenFilter;

use Pucene\Component\Analysis\Token;

interface TokenFilterInterface
{
    /**
     * @param Token $token
     *
     * @return Token[]
     */
    public function filter(Token $token);
}
