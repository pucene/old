<?php

namespace Pucene\Tests\Functional\Comparison;

use Pucene\Component\QueryBuilder\Query\FullText\MatchQuery;
use Pucene\Component\QueryBuilder\Search;

/**
 * This testcase compares elasticsearch with pucene results for the "match" query.
 */
class MatchComparisonTest extends ComparisonTestCase
{
    public function testSearchMatch()
    {
        $search = new Search(new MatchQuery('title', 'museum lyon'));
        $search->setSize(20);

        $this->assertSearch($search);
    }
}
