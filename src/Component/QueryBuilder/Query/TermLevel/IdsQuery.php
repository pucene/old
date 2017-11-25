<?php

namespace Pucene\Component\QueryBuilder\Query\TermLevel;

use Pucene\Component\QueryBuilder\Query\QueryInterface;

class IdsQuery implements QueryInterface
{
    /**
     * @var string[]
     */
    private $values;

    /**
     * @var string
     */
    private $type;

    public function __construct(array $values, string $type = null)
    {
        $this->values = $values;
        $this->type = $type;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function getType(): ?string
    {
        return $this->type;
    }
}
