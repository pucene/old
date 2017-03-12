<?php

namespace Pucene\Analysis\TokenFilter;

use Pucene\Analysis\Token;

class ChainTokenFilter implements TokenFilterInterface
{
    /**
     * @var TokenFilterInterface[]
     */
    private $filters;

    /**
     * @param TokenFilterInterface[] $filters
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @param Token $token
     *
     * @return Token[]
     */
    public function filter(Token $token)
    {
        $tokens = [$token];
        foreach ($this->filters as $filter) {
            $tokens = $this->doFilter($filter, $tokens);
        }

        return $tokens;
    }

    /**
     * @param TokenFilterInterface $filter
     * @param Token[] $tokens
     *
     * @return Token[]
     */
    private function doFilter(TokenFilterInterface $filter, array $tokens)
    {
        $result = [];
        foreach ($tokens as $token) {
            $result = array_merge($result, $filter->filter($token));
        }

        return $result;
    }
}
