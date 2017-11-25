<?php

namespace Pucene\Component\Analysis\TokenFilter;

use Pucene\Component\Analysis\Token;

/**
 * TODO: stopwords_path, ignore_case, remove_trailing.
 */
class StopTokenFilter implements TokenFilterInterface
{
    /**
     * @var string[]
     */
    private $stopWords;

    /**
     * @param string[] $stopWords
     */
    public function __construct(array $stopWords = StopWords::ENGLISH)
    {
        $this->stopWords = $stopWords;
    }

    public function filter(Token $token): array
    {
        if (in_array($token->getTerm(), $this->stopWords)) {
            return [];
        }

        return [$token];
    }
}
