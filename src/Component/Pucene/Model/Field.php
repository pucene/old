<?php

namespace Pucene\Component\Pucene\Model;

use Pucene\Component\Analysis\Token;
use Pucene\Component\Mapping\Types;

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
     * @var mixed
     */
    private $value;

    /**
     * @var string
     */
    private $type;

    /**
     * @param Token[] $tokens
     * @param mixed $value
     */
    public function __construct($name, array $tokens, $value, string $type = Types::TEXT)
    {
        $this->name = $name;
        $this->tokens = $tokens;
        $this->value = $value;
        $this->type = $type;
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

    public function getNumberOfTerms(): int
    {
        return count($this->tokens);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
