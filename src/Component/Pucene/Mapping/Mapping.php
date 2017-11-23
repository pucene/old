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
     * @var TypeGuesser
     */
    private $guesser;

    /**
     * @var AnalyzerInterface
     */
    private $analyzer;

    public function __construct(array $indexMapping, TypeGuesser $guesser, AnalyzerInterface $analyzer)
    {
        $this->indexMapping = $indexMapping;
        $this->guesser = $guesser;
        $this->analyzer = $analyzer;
    }

    /**
     * @param mixed $value
     */
    public function getTypeForField(string $index, string $documentType, string $field, $value): string
    {
        if (!array_key_exists($index, $this->indexMapping)
            || !array_key_exists($documentType, $this->indexMapping[$index]['mappings'])
            || !array_key_exists($field, $this->indexMapping[$index]['mappings'][$documentType]['properties'])
            || null === $this->indexMapping[$index]['mappings'][$documentType]['properties'][$field]['type']
        ) {
            return $this->indexMapping[$index]['mappings'][$documentType]['properties'][$field]['type'] = $this->guesser->guess($value);
        }

        return $this->indexMapping[$index]['mappings'][$documentType]['properties'][$field]['type'];
    }

    public function getAnalyzerForField(string $field, string $type): ?AnalyzerInterface
    {
        if ($type !== Types::TEXT) {
            return null;
        }

        return $this->analyzer;
    }
}
