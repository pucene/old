<?php

namespace Pucene\Tests\src\Functional\Comparison;

use Pucene\Component\QueryBuilder\Query\TermLevel\IdsQuery;
use Pucene\Component\QueryBuilder\Search;
use Pucene\Tests\Functional\Comparison\ComparisonTestCase;

/**
 * This testcase compares elasticsearch with pucene results for the "ids" query.
 */
class IdsComparisonTest extends ComparisonTestCase
{
    public function testValues()
    {
        $this->assertSearch(new Search(new IdsQuery(['Q511'])));
    }

    public function testValuesIds()
    {
        $this->assertSearch(new Search(new IdsQuery(['Q511'], 'my_type')));
    }
}
