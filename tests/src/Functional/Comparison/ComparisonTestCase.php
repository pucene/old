<?php

namespace Pucene\Tests\Functional\Comparison;

use Pucene\Component\Client\IndexInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class ComparisonTestCase extends KernelTestCase
{
    /**
     * @var IndexInterface
     */
    protected $puceneIndex;

    /**
     * @var IndexInterface
     */
    protected $elasticsearchIndex;

    protected function setUp()
    {
        $this->bootKernel();

        $pucene = $this->get('pucene.pucene.client');
        $this->puceneIndex = $pucene->get('my_index');

        $elasticsearch = $this->get('pucene.elasticsearch.client');
        $this->elasticsearchIndex = $elasticsearch->get('my_index');
    }

    protected function get($id)
    {
        return self::$kernel->getContainer()->get($id);
    }
}
