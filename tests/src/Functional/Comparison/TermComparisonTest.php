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

    public function testSearchTermKeyword()
    {
        $this->assertSearch(new Search(new TermQuery('rawTitle', 'George Washington')));
    }

    public function testSearchTermBoolean()
    {
        $this->assertSearch(new Search(new TermQuery('enabled', true)));
    }

    public function testSearchTermFloat()
    {
        $this->assertSearch(new Search(new TermQuery('seed', 0.99)));
    }

    public function testSearchTermInteger()
    {
        $this->assertSearch(new Search(new TermQuery('pageId', 315)));
    }
}
