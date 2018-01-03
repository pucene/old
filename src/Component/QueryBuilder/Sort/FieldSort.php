<?php

namespace Pucene\Component\QueryBuilder\Sort;

class FieldSort implements SortInterface
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $order;

    public function __construct(string $field, string $order = self::ASC)
    {
        $this->field = $field;
        $this->order = $order;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getOrder(): string
    {
        return $this->order;
    }
}
