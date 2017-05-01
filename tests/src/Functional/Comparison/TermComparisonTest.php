<?php

namespace Pucene\Tests\Functional\Comparison;

use Pucene\Component\QueryBuilder\Query\TermLevel\TermQuery;
use Pucene\Component\QueryBuilder\Search;

/**
 * This testcase compares elasticsearch with pucene results for the "term" query.
 */
class TermComparisonTest extends ComparisonTestCase
{
    public function testSearchTerm()
    {
        $this->assertSearch(new Search(new TermQuery('title', 'museum')));
    }
}
