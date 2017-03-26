<?php

namespace Pucene\Analysis\TokenFilter;

use Pucene\Analysis\Token;

class StandardTokenFilter implements TokenFilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function filter(Token $token)
    {
        return [$token];
    }
}
