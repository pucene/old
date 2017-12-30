<?php

namespace Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis;

class DocumentLike
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $index;

    public function __construct(string $id, ?string $type = null, ?string $index = null)
    {
        $this->id = $id;
        $this->type = $type;
        $this->index = $index;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getIndex(): ?string
    {
        return $this->index;
    }
}
