<?php

namespace Pucene\Component\Analysis\TokenFilter;

use Pucene\Component\Analysis\Token;

class StandardTokenFilter implements TokenFilterInterface
{
    public function filter(Token $token): array
    {
        return [$token];
    }
}
