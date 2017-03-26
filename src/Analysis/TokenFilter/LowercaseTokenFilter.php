<?php

namespace Pucene\Analysis\TokenFilter;

use Pucene\Analysis\Token;

class LowercaseTokenFilter implements TokenFilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function filter(Token $token)
    {
        return [
            new Token(
                strtolower($token->getTerm()),
                $token->getStartOffset(),
                $token->getEndOffset(),
                $token->getType(),
                $token->getPosition()
            ),
        ];
    }
}
