<?php

namespace Pucene\Tests\Functional\Pucene;

use Pucene\Component\Pucene\PuceneClient;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PuceneClientTest extends KernelTestCase
{
    /**
     * @var PuceneClient
     */
    private $puceneClient;

    protected function setUp()
    {
        parent::setUp();

        $this->bootKernel();

        $this->puceneClient = $this->get('pucene.pucene.client');
    }

    public function testGetIndexNames()
    {
        $indices = $this->puceneClient->getIndexNames();

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
