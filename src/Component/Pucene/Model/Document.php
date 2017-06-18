<?php

namespace Pucene\Component\Pucene\Model;

class Document
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

    /**
     * @var string
     */
    private $document;

    /**
     * @var int
     */
    private $score;

    /**
     * @param string $id
     * @param string $type
     * @param string $index
     * @param string $document
     * @param int $score
     */
    public function __construct($id, $type, $index, $document, $score = null)
    {
        $this->id = $id;
        $this->type = $type;
        $this->index = $index;
        $this->document = $document;
        $this->score = $score;
    }

    /**
     * Returns id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns index.
     *
     * @return string
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Returns document.
     *
     * @return string
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Returns score.
     *
     * @return int
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Converts document to array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            '_id' => $this->id,
            '_type' => $this->type,
            '_index' => $this->index,
            '_score' => $this->score,
            '_source' => $this->document,
        ];
    }
}
