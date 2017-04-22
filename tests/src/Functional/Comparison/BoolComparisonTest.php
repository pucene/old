<?php

namespace Pucene\Tests\Functional\Comparison;

use Pucene\Component\QueryBuilder\Query\Compound\BoolQuery;
use Pucene\Component\QueryBuilder\Query\TermLevel\TermQuery;
use Pucene\Component\QueryBuilder\Search;

/**
 * This testcase compares elasticsearch with pucene results for the "bool" query.
 */
class BoolComparisonTest extends ComparisonTestCase
{
    public function testShouldTerm()
    {
        $query = new BoolQuery();
        $query->should(new TermQuery('title', 'museum'));

        $this->assertSearch(new Search($query));
    }

    public function testShouldTerms()
    {
        $query = new BoolQuery();
        $query->should(new TermQuery('title', 'museum'));
        $query->should(new TermQuery('title', 'arts'));

        $this->assertSearch(new Search($query));
    }
}
