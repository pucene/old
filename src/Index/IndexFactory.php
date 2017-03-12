<?php

namespace Pucene\Index;

use Pucene\InvertedIndex\InvertedIndex;

class IndexFactory
{
    /**
     * @var InvertedIndex
     */
    protected $invertedIndex;

    /**
     * @param InvertedIndex $invertedIndex
     */
    public function __construct(InvertedIndex $invertedIndex)
    {
        $this->invertedIndex = $invertedIndex;
    }

    public function create($name)
    {
        return new Index($this->invertedIndex, $name);
    }
}
