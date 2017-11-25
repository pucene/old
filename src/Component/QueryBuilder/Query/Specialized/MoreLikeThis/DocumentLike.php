<?php

namespace Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis;

class DocumentLike
{
    /**
     * @var string
     */
    private $index;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $id;

    public function __construct(string $index, string $type, string $id)
    {
        $this->index = $index;
        $this->type = $type;
        $this->id = $id;
    }

    public function getIndex(): string
    {
        return $this->index;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
