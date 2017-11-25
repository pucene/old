<?php

namespace Pucene\Component\Analysis;

class Token
{
    /**
     * @var string
     */
    protected $term;

    /**
     * @var int
     */
    protected $startOffset;

    /**
     * @var int
     */
    protected $endOffset;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var int
     */
    protected $position;

    public function __construct(string $term, int $startOffset, int $endOffset, string $type, int $position)
    {
        $this->term = $term;
        $this->startOffset = $startOffset;
        $this->endOffset = $endOffset;
        $this->type = $type;
        $this->position = $position;
    }

    public function getTerm(): string
    {
        return $this->term;
    }

    public function getEncodedTerm(): string
    {
        return utf8_encode($this->term);
    }

    public function getStartOffset(): int
    {
        return $this->startOffset;
    }

    public function getEndOffset(): int
    {
        return $this->endOffset;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}
