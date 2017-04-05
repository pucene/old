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
     * @param Document $document
     * @param Field[] $fields
     */
    public function __construct(Document $document, array $fields)
    {
        $this->document = $document;
        $this->fields = $fields;
    }

    /**
     * Returns document.
     *
     * @return Document
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Returns fields.
     *
     * @return Field[]
     */
    public function getFields()
    {
        return $this->fields;
    }
}
