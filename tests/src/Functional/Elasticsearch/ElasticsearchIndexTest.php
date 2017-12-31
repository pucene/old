<?php

namespace Pucene\Tests\Functional\Elasticsearch;

use Pucene\Component\Elasticsearch\ElasticsearchIndex;
use Pucene\Component\QueryBuilder\Query\TermLevel\TermQuery;
use Pucene\Component\QueryBuilder\Search;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ElasticsearchIndexTest extends KernelTestCase
{
    /**
     * @var ElasticsearchIndex
     */
    private $elasticsearchIndex;

    protected function setUp()
    {
        parent::setUp();

        $this->bootKernel();

        $this->elasticsearchIndex = $this->get('pucene.elasticsearch.client')->get('my_index');
    }

    public function testCount()
    {
        $total = $this->elasticsearchIndex->count(new Search(new TermQuery('title', 'museum')), 'my_type');

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
