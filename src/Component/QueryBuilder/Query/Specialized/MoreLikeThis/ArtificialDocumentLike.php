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

    public function __construct(string $index, string $type, array $document)
    {
        $this->index = $index;
        $this->type = $type;
        $this->document = $document;
    }

    public function getIndex(): string
    {
        return $this->index;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDocument(): array
    {
        return $this->document;
    }
}
