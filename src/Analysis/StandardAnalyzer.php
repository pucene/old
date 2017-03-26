<?php

namespace Pucene\Analysis;

use Pucene\Analysis\CharacterFilter\ChainCharacterFilter;
use Pucene\Analysis\CharacterFilter\StandardCharacterFilter;
use Pucene\Analysis\TokenFilter\ChainTokenFilter;
use Pucene\Analysis\TokenFilter\LowercaseTokenFilter;
use Pucene\Analysis\TokenFilter\StandardTokenFilter;
use Pucene\Analysis\Tokenizer\StandardTokenizer;

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
