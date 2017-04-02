<?php

namespace Pucene\Component\Pucene\Model;

use Pucene\Component\Analysis\Token;

class Field
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Token[]
     */
    private $tokens;

    /**
     * @param string $name
     * @param Token[] $tokens
     */
    public function __construct($name, array $tokens)
    {
        $this->name = $name;
        $this->tokens = $tokens;
    }

    /**
     * Returns name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns tokens.
     *
     * @return Token[]
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * Returns number of tokens.
     *
     * @return int
     */
    public function getNumberOfTerms()
    {
        return count($this->tokens);
    }
}
