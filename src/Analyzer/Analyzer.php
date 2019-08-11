<?php

namespace Pucene\Analyzer;

class Analyzer implements AnalyzerInterface
{
    /**
     * @var CharacterFilterInterface[]
     */
    private $characterFilters;

    /**
     * @var TokenizerInterface
     */
    private $tokenizer;

    /**
     * @var TokenFilterInterface[]
     */
    private $tokenFilters;

    public function __construct(array $characterFilters, TokenizerInterface $tokenizer, array $tokenFilters)
    {
        $this->characterFilters = $characterFilters;
        $this->tokenizer = $tokenizer;
        $this->tokenFilters = $tokenFilters;
    }

    public function analyze(string $sentence): array
    {
        foreach ($this->characterFilters as $characterFilter) {
            $sentence = $characterFilter->filter($sentence);
        }

        $tokens = $this->tokenizer->tokenize($sentence);

        foreach ($this->tokenFilters as $tokenFilter) {
            $tokens = $tokenFilter->filter($tokens);
        }

        return $tokens;
    }
}
