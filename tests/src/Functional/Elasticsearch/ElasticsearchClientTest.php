<?php

namespace Pucene\Tests\Functional\Elasticsearch;

use Pucene\Component\Elasticsearch\ElasticsearchClient;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ElasticsearchClientTest extends KernelTestCase
{
    /**
     * @var ElasticsearchClient
     */
    private $elasticsearchClient;

    protected function setUp()
    {
        parent::setUp();

        $this->bootKernel();

        $this->elasticsearchClient = $this->get('pucene.elasticsearch.client');
    }

    public function testGetIndexNames()
    {
        $indices = $this->elasticsearchClient->getIndexNames();

        $this->assertContains('my_index', $indices);
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
