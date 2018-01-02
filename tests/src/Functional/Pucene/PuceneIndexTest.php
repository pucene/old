<?php

namespace Pucene\Tests\Functional\Pucene;

use Pucene\Component\Pucene\PuceneIndex;
use Pucene\Component\QueryBuilder\Query\TermLevel\TermQuery;
use Pucene\Component\QueryBuilder\Search;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PuceneIndexTest extends KernelTestCase
{
    /**
     * @var PuceneIndex
     */
    private $puceneIndex;

    protected function setUp()
    {
        parent::setUp();

        $this->bootKernel();

        $this->puceneIndex = $this->get('pucene.pucene.client')->get('my_index');
    }

    public function testCount()
    {
        $total = $this->puceneIndex->count(new Search(new TermQuery('title', 'museum')), 'my_type');

        $this->assertEquals(5, $total);
    }

    /**
     * Gets a service.
     *
     * @param string $id
     *
     * @return object
     */
    protected function get($id)
    {
        return self::$kernel->getContainer()->get($id);
    }
}
