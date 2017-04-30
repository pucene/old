<?php

namespace Pucene\Tests\Functional\Comparison;

use Pucene\Component\Pucene\Dbal\QueryBuilder\Query\Compound\BooleanQuery;
use Pucene\Component\QueryBuilder\Query\Compound\Boolean;
use Pucene\Component\QueryBuilder\Query\Compound\BoolQuery;
use Pucene\Component\QueryBuilder\Query\FullText\Match;
use Pucene\Component\QueryBuilder\Query\FullText\MatchQuery;
use Pucene\Component\QueryBuilder\Query\TermLevel\Term;
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

    public function testShouldMatch()
    {
        $query = new BoolQuery();
        $query->should(new MatchQuery('title', 'Museum Lyon'));

        $this->assertSearch((new Search($query))->setSize(20));
    }

    public function testShouldMatches()
    {
        $query = new BoolQuery();
        $query->should(new MatchQuery('title', 'Museum Lyon'));
        $query->should(new MatchQuery('title', 'Art Museum'));

        $this->assertSearch((new Search($query))->setSize(50));
    }

    public function testShouldMatchAndTerm()
    {
        $query = new Boolean();
        $query->should(new Match('title', 'Museum Lyon'));
        $query->should(new Term('title', 'arts'));

        $this->assertSearch((new Search($query))->setSize(50));
    }

    public function testShouldNestedBool()
    {
        $nested = new Boolean();
        $nested->should(new Term('title', 'museum'));
        $nested->should(new Term('title', 'lyon'));

        $query = new Boolean();
        $query->should($nested);
        $query->should(new Term('title', 'arts'));

        $this->assertSearch((new Search($query))->setSize(50));
    }
}
