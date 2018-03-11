<?php

namespace Pucene\Tests\Functional\Comparison;

use Pucene\Component\QueryBuilder\Query\TermLevel\PrefixQuery;
use Pucene\Component\QueryBuilder\Search;

/**
 * This testcase compares elasticsearch with pucene results for the "term" query.
 */
class PrefixComparisonTest extends ComparisonTestCase
{
    public function testSearchPrefix()
    {
        $this->assertSearch(new Search(new PrefixQuery('title', 'muse')));
    }
}
