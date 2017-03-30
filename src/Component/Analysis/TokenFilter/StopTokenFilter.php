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
    public function __construct($stopWords = StopWords::ENGLISH)
    {
        $this->stopWords = $stopWords;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(Token $token)
    {
        if (in_array($token->getTerm(), $this->stopWords)) {
            return [];
        }

        return [$token];
    }
}
