<?php

namespace Pucene\Component\QueryBuilder\Sort;

class IdSort extends FieldSort implements SortInterface
{
    /**
     * @var string
     */
    private $order;

    public function __construct(string $order = self::ASC)
    {
        parent::__construct('_uid', $order);

        $this->order = $order;
    }

    public function getOrder(): string
    {
        return $this->order;
    }
}
