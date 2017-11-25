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
     * @param Token[] $tokens
     */
    public function __construct(string $name, array $tokens)
    {
        $this->name = $name;
        $this->tokens = $tokens;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Token[]
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }

    /**
     * @return int
     */
    public function getNumberOfTerms(): int
    {
        return count($this->tokens);
    }
}
