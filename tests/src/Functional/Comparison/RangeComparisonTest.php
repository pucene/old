<?php

namespace Pucene\Tests\Functional\Comparison;

use Pucene\Component\QueryBuilder\Query\TermLevel\RangeQuery;
use Pucene\Component\QueryBuilder\Search;

/**
 * This testcase compares elasticsearch with pucene results for the "range" query.
 */
class RangeComparisonTest extends ComparisonTestCase
{
    public function testSearchRangeLte()
    {
        $this->assertSearch((new Search((new RangeQuery('pageId'))->lte(500)))->setSize(50));
    }

    public function testSearchRangeLt()
    {
        $this->assertSearch((new Search((new RangeQuery('pageId'))->lt(500)))->setSize(50));
    }

    public function testSearchRangeGte()
    {
        $this->assertSearch((new Search((new RangeQuery('pageId'))->gte(53000)))->setSize(250));
    }

    public function testSearchRangeGt()
    {
        $this->assertSearch((new Search((new RangeQuery('pageId'))->gt(53000)))->setSize(250));
    }

    public function testSearchRangeGteLt()
    {
        $this->assertSearch((new Search((new RangeQuery('pageId'))->gte(315)->lt(316))));
    }

    public function testSearchRangeGteLte()
    {
        $this->assertSearch((new Search((new RangeQuery('pageId'))->gte(315)->lte(316))));
    }

    public function testSearchRangeFloat()
    {
        $this->assertSearch((new Search((new RangeQuery('seed'))->gte(0.90)->lte(0.91)))->setSize(200));
    }

    public function testSearchRangeDate()
    {
        $this->assertSearch((new Search((new RangeQuery('modified'))->gte('2017-11-21T09:39:53Z')->lte('2017-11-21T09:40:00Z')))->setSize(200));
    }
}
