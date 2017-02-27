<?php

namespace Pucene\Index;

use Pucene\Analysis\AnalyzerInterface;
use Pucene\InvertedIndex\InvertedIndex;

class IndexFactory
{
    /**
     * @var AnalyzerInterface
     */
    protected $analyzer;

    /**
     * @var InvertedIndex
     */
    protected $invertedIndex;

    /**
     * @param AnalyzerInterface $analyzer
     * @param InvertedIndex $invertedIndex
     */
    public function __construct(AnalyzerInterface $analyzer, InvertedIndex $invertedIndex)
    {
        $this->analyzer = $analyzer;
        $this->invertedIndex = $invertedIndex;
    }

    public function create($name)
    {
        return new Index($this->analyzer, $this->invertedIndex, $name);
    }
}
