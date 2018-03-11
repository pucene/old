<?php

namespace Pucene\Tests\Functional\Comparison;

use Pucene\Component\QueryBuilder\Query\FullText\MatchPhrasePrefixQuery;
use Pucene\Component\QueryBuilder\Search;

/**
 * This testcase compares elasticsearch with pucene results for the "match_phrase_prefix" query.
 */
class MatchPhrasePrefixComparisonTest extends ComparisonTestCase
{
    public function testSearchMatchPhrasePrefix()
    {
        $this->assertSearch(new Search(new MatchPhrasePrefixQuery('title', 'museum of m')));
    }
}
