<?php

namespace Pucene\Analysis;

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

    /**
     * @param string $term
     * @param int $startOffset
     * @param int $endOffset
     * @param string $type
     * @param int $position
     */
    public function __construct($term, $startOffset, $endOffset, $type, $position)
    {
        $this->term = $term;
        $this->startOffset = $startOffset;
        $this->endOffset = $endOffset;
        $this->type = $type;
        $this->position = $position;
    }

    /**
     * Returns term.
     *
     * @return mixed
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * Returns startOffset.
     *
     * @return mixed
     */
    public function getStartOffset()
    {
        return $this->startOffset;
    }

    /**
     * Returns endOffset.
     *
     * @return mixed
     */
    public function getEndOffset()
    {
        return $this->endOffset;
    }

    /**
     * Returns type.
     *
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns position.
     *
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }
}
