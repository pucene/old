<?php

namespace Pucene\Component\Math\Expression;

use Pucene\Component\Math\ExpressionInterface;

class CompositeExpression implements ExpressionInterface
{
    /**
     * @var ExpressionInterface[]
     */
    private $parts;

    /**
     * @var string
     */
    private $glue;

    /**
     * @param ExpressionInterface[] $parts
     * @param string $glue
     */
    public function __construct(array $parts, string $glue)
    {
        $this->parts = $parts;
        $this->glue = $glue;
    }

    public function add(ExpressionInterface $part): self
    {
        $this->parts[] = $part;

        return $this;
    }

    public function __toString(): string
    {
        return sprintf('(%s)', implode($this->glue, $this->parts));
    }
}
