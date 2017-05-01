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

    /**
     * @param string[] $values
     * @param string $type
     */
    public function __construct(array $values, string $type = null)
    {
        $this->values = $values;
        $this->type = $type;
    }

    /**
     * Returns values.
     *
     * @return \string[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * Returns type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
