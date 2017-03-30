<?php

namespace Pucene\Component\Analysis\TokenFilter;

use Pucene\Component\Analysis\Token;

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
