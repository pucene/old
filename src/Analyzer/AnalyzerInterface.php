<?php

namespace Pucene\Analyzer;

interface AnalyzerInterface
{
    /**
     * @return string[]
     */
    public function analyze(string $sentence): array;
}
