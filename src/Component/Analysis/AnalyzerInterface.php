<?php

namespace Pucene\Component\Analysis;

interface AnalyzerInterface
{
    /**
     * Generate token from field-content.
     *
     * @param string $fieldContent
     *
     * @return Token[]
     */
    public function analyze($fieldContent);
}
