<?php

namespace Pucene\Component\Analysis\TokenFilter;

use Pucene\Component\Analysis\Token;

class LowercaseTokenFilter implements TokenFilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function filter(Token $token)
    {
        return [
            new Token(
                mb_strtolower($token->getTerm()),
                $token->getStartOffset(),
                $token->getEndOffset(),
                $token->getType(),
                $token->getPosition()
            ),
        ];
    }
}
