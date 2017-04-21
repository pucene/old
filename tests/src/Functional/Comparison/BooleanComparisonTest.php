<?php

namespace Pucene\Tests\Functional\Comparison;

use Pucene\Component\QueryBuilder\Query\Compound\Boolean;
use Pucene\Component\QueryBuilder\Query\TermLevel\Term;
use Pucene\Component\QueryBuilder\Search;

/**
 * This testcase compares elasticsearch with pucene results for the "bool" query.
 */
class BooleanComparisonTest extends ComparisonTestCase
{
    public function testShouldTerm()
    {
        $query = new Boolean();
        $query->should(new Term('title', 'museum'));

        $this->assertSearch(new Search($query));
    }

    public function testShouldTerms()
    {
        $query = new Boolean();
        $query->should(new Term('title', 'museum'));
        $query->should(new Term('title', 'arts'));

        $this->assertSearch(new Search($query));
    }
}
