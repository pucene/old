<?php

namespace Pucene\Component\QueryBuilder\Sort;

class IdSort implements SortInterface
{
    /**
     * @var string
     */
    private $order;

    public function __construct(string $order = self::ASC)
    {
        $this->order = $order;
    }

    public function getOrder(): string
    {
        return $this->order;
    }
}
