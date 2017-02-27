<?php

namespace Pucene\Analysis;

class Token
{
    /**
     * @var string
     */
    protected $token;

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
     * @param string $token
     * @param int $startOffset
     * @param int $endOffset
     * @param string $type
     * @param int $position
     */
    public function __construct($token, $startOffset, $endOffset, $type, $position)
    {
        $this->token = $token;
        $this->startOffset = $startOffset;
        $this->endOffset = $endOffset;
        $this->type = $type;
        $this->position = $position;
    }

    /**
     * Returns token.
     *
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
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
