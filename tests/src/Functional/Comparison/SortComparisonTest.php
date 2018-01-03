<?php

namespace Pucene\Tests\Functional\Comparison;

use Pucene\Component\QueryBuilder\Query\TermLevel\TermQuery;
use Pucene\Component\QueryBuilder\Search;
use Pucene\Component\QueryBuilder\Sort\FieldSort;
use Pucene\Component\QueryBuilder\Sort\IdSort;
use Pucene\Component\QueryBuilder\Sort\SortInterface;

/**
 * This testcase compares elasticsearch with pucene results when sorting.
 */
class SortComparisonTest extends ComparisonTestCase
{
    public function testIdSort()
    {
        $search = new Search(new TermQuery('title', 'museum'));
        $search->addSort(new IdSort());

        $this->assertSearch($search);
    }

    public function testIdSortDesc()
    {
        $search = new Search(new TermQuery('title', 'museum'));
        $search->addSort(new IdSort(SortInterface::DESC));

        $this->assertSearch($search);
    }

    public function testFieldSort()
    {
        $search = new Search(new TermQuery('title', 'museum'));
        $search->addSort(new FieldSort('title.raw'));

        $this->assertSearch($search);
    }

    public function testFieldSortDesc()
    {
        $search = new Search(new TermQuery('title', 'museum'));
        $search->addSort(new FieldSort('title.raw', SortInterface::DESC));

        $this->assertSearch($search);
    }
}
