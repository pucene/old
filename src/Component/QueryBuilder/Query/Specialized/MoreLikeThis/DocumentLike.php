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

    /**
     * @param string $index
     * @param string $type
     * @param string $id
     */
    public function __construct($index, $type, $id)
    {
        $this->index = $index;
        $this->type = $type;
        $this->id = $id;
    }

    /**
     * Returns index.
     *
     * @return string
     */
    public function getIndex(): string
    {
        return $this->index;
    }

    /**
     * Returns type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Returns id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}
