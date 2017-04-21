<?php

namespace Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis;

class ArtificialDocumentLike
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
     * @var array
     */
    private $document;

    /**
     * @param string $index
     * @param string $type
     * @param array $document
     */
    public function __construct($index, $type, array $document)
    {
        $this->index = $index;
        $this->type = $type;
        $this->document = $document;
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
     * Returns document.
     *
     * @return array
     */
    public function getDocument(): array
    {
        return $this->document;
    }
}
