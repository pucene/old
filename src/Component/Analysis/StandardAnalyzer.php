<?php

namespace Pucene\Component\Analysis;

use Pucene\Component\Analysis\CharacterFilter\ChainCharacterFilter;
use Pucene\Component\Analysis\CharacterFilter\StandardCharacterFilter;
use Pucene\Component\Analysis\TokenFilter\ChainTokenFilter;
use Pucene\Component\Analysis\TokenFilter\LowercaseTokenFilter;
use Pucene\Component\Analysis\TokenFilter\StandardTokenFilter;
use Pucene\Component\Analysis\Tokenizer\StandardTokenizer;

class StandardAnalyzer extends Analyzer
{
    public function __construct()
    {
        parent::__construct(
            new ChainCharacterFilter(
                [
                    new StandardCharacterFilter(),
                ]
            ),
            $this->tokenizer = new StandardTokenizer(),
            new ChainTokenFilter(
                [
                    new StandardTokenFilter(),
                    new LowercaseTokenFilter(),
                ]
            )
        );
    }
}
