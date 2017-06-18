<?php

namespace Pucene\Tests\Functional\Comparison;

use Pucene\Component\Pucene\Dbal\Interpreter\Fuzzy;
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

    public function testSearchMatchFuzzy()
    {
        $search = new Search((new MatchQuery('title', 'museum'))->setFuzzy(1));
        $search->setSize(20);

        $this->assertSearch($search);
    }

    public function testSearchMatchFuzzy2()
    {
        $search = new Search((new MatchQuery('title', 'museum'))->setFuzzy(2));
        $search->setSize(20);

        $this->assertSearch($search);
    }

    public function testSearchMatchFuzzyAuto()
    {
        $search = new Search((new MatchQuery('title', 'museum'))->setFuzzy(Fuzzy::MODE_AUTO));
        $search->setSize(20);

        $this->assertSearch($search);
    }
}
