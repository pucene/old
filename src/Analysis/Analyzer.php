<?php

namespace Pucene\Analysis;

use Pucene\Analysis\CharacterFilter\CharacterFilterInterface;
use Pucene\Analysis\TokenFilter\TokenFilterInterface;
use Pucene\Analysis\Tokenizer\TokenizerInterface;

class Analyzer implements AnalyzerInterface
{
    /**
     * @var CharacterFilterInterface
     */
    protected $characterFilter;

    /**
     * @var TokenizerInterface
     */
    protected $tokenizer;

    /**
     * @var TokenFilterInterface
     */
    protected $tokenFilter;

    /**
     * @param $characterFilter
     * @param TokenizerInterface $tokenizer
     * @param TokenFilterInterface $tokenFilter
     */
    public function __construct(
        CharacterFilterInterface $characterFilter,
        TokenizerInterface $tokenizer,
        TokenFilterInterface $tokenFilter
    ) {
        $this->characterFilter = $characterFilter;
        $this->tokenizer = $tokenizer;
        $this->tokenFilter = $tokenFilter;
    }

    /**
     * {@inheritdoc}
     */
    public function analyze($fieldContent)
    {
        $input = $this->characterFilter->filter($fieldContent);
        $tokens = $this->tokenizer->tokenize($input);

        $result = [];
        foreach ($tokens as $token) {
            $result = array_merge($result, $this->tokenFilter->filter($token));
        }

        return $result;
    }
}
