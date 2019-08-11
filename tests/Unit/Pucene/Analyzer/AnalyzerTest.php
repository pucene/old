<?php

namespace \PuceneTests\Unit\Analyzer;

use Pucene\Analyzer\Analyzer;
use Pucene\Analyzer\CharacterFilterInterface;
use Pucene\Analyzer\TokenFilterInterface;
use Pucene\Analyzer\TokenizerInterface;
use PHPUnit\Framework\TestCase;

class AnalyzerTest extends TestCase
{
    public function testAnalyze(): void
    {
        $characterFilter1 = $this->prophesize(CharacterFilterInterface::class);
        $characterFilter1->filter('The QUICK brown foxes jumped over the lazy dog!')
            ->willReturn('The QUICK brown foxes jumped over the lazy dog');
        $characterFilter2 = $this->prophesize(CharacterFilterInterface::class);
        $characterFilter2->filter('The QUICK brown foxes jumped over the lazy dog')
            ->willReturn('the quick brown foxes jumped over the lazy dog');

        $tokenizer = $this->prophesize(TokenizerInterface::class);
        $tokenizer->tokenize('the quick brown foxes jumped over the lazy dog')
            ->willReturn(['the', 'quick', 'brown', 'foxes', 'jumped', 'over', 'the', 'lazy', 'dog']);

        $tokenFilter1 = $this->prophesize(TokenFilterInterface::class);
        $tokenFilter1->filter(['the', 'quick', 'brown', 'foxes', 'jumped', 'over', 'the', 'lazy', 'dog'])
            ->willReturn(['the', 'quick', 'brown', 'foxes', 'jumped', 'over', 'lazy', 'dog']);
        $tokenFilter2 = $this->prophesize(TokenFilterInterface::class);
        $tokenFilter2->filter(['the', 'quick', 'brown', 'foxes', 'jumped', 'over', 'lazy', 'dog'])
            ->willReturn(['quick', 'brown', 'fox', 'jump', 'over', 'lazi', 'dog']);

        $analyzer = new Analyzer(
            [$characterFilter1->reveal(), $characterFilter2->reveal()],
            $tokenizer->reveal(),
            [$tokenFilter1->reveal(), $tokenFilter2->reveal()]
        );

        $this->assertEquals(
            ['quick', 'brown', 'fox', 'jump', 'over', 'lazi', 'dog'],
            $analyzer->analyze('The QUICK brown foxes jumped over the lazy dog!')
        );
    }
}
