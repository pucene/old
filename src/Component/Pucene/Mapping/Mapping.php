<?php

namespace Pucene\Component\Pucene\Mapping;

use Pucene\Component\Analysis\AnalyzerInterface;
use Pucene\Component\Mapping\Types;

class Mapping
{
    /**
     * @var array
     */
    private $indexMapping;

    /**
     * @var AnalyzerInterface
     */
    private $textAnalyzer;

    /**
     * @var AnalyzerInterface
     */
    private $keywordAnalyzer;

    /**
     * @var array
     */
    private $fields;

    public function __construct(
        array $indexMapping,
        AnalyzerInterface $textAnalyzer,
        AnalyzerInterface $keywordAnalyzer
    ) {
        $this->indexMapping = $indexMapping;
        $this->textAnalyzer = $textAnalyzer;
        $this->keywordAnalyzer = $keywordAnalyzer;

        $this->fields = [];
        foreach ($indexMapping as $name => $index) {
            $this->fields[$name] = [];
            foreach ($index['mappings'] as $type) {
                foreach ($type['properties'] as $fieldName => $field) {
                    $this->fields[$name][$fieldName] = $field;
                }
            }
        }
    }

    public function getTypeForField(string $index, string $field): ?string
    {
        if (!array_key_exists($index, $this->fields) || !array_key_exists($field, $this->fields[$index])) {
            return null;
        }

        return $this->fields[$index][$field]['type'];
    }

    public function getAnalyzerForField(string $field, string $type): ?AnalyzerInterface
    {
        if (Types::TEXT === $type) {
            return $this->textAnalyzer;
        }

        if (Types::KEYWORD === $type) {
            return $this->keywordAnalyzer;
        }

        return null;
    }
}
