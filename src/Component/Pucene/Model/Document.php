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
     * @var array
     */
    private $document;

    /**
     * @var float
     */
    private $score;

    public function __construct(string $id, string $type, string $index, array $document, ?float $score = null)
    {
        $this->id = $id;
        $this->type = $type;
        $this->index = $index;
        $this->document = $document;
        $this->score = $score;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getIndex(): string
    {
        return $this->index;
    }

    public function getDocument(): array
    {
        return $this->document;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

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
