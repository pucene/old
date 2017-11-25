<?php

namespace Pucene\Component\Pucene\Model;

class Analysis
{
    /**
     * @var Document
     */
    private $document;

    /**
     * @var Field[]
     */
    private $fields;

    /**
     * @param Field[] $fields
     */
    public function __construct(Document $document, array $fields)
    {
        $this->document = $document;
        $this->fields = $fields;
    }

    public function getDocument(): Document
    {
        return $this->document;
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }
}
