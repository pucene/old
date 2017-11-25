<?php

namespace Pucene\Component\Analysis;

interface AnalyzerInterface
{
    /**
     * Generate token from field-content.
     *
     * @return Token[]
     */
    public function analyze(string $fieldContent): array;
}
