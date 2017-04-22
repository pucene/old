<?php

namespace Pucene\Tests\Functional\Comparison;

use Pucene\Component\Client\IndexInterface;
use Pucene\Component\QueryBuilder\Search;
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

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->bootKernel();

        $pucene = $this->get('pucene.pucene.client');
        $this->puceneIndex = $pucene->get('my_index');

        $elasticsearch = $this->get('pucene.elasticsearch.client');
        $this->elasticsearchIndex = $elasticsearch->get('my_index');
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

    /**
     * Normalizes result from client.
     *
     * @param array $hits
     *
     * @return array
     */
    protected function normalize($hits)
    {
        $result = [];
        $position = 0;
        foreach ($hits as $hit) {
            $score = $hit['_score'];
            unset($hit['_score']);
            unset($hit['sort']);
            $result[$hit['_id']] = ['position' => $position++, '_score' => $score, 'document' => $hit];
        }

        return $result;
    }

    /**
     * Execute given search on both (elasticsearch and pucene) and compares result.
     *
     * @param Search $search
     */
    protected function assertSearch(Search $search)
    {
        $elasticsearchResult = $this->elasticsearchIndex->search($search, 'my_type');
        $puceneResult = $this->puceneIndex->search($search, 'my_type');

        $this->assertEquals(count($elasticsearchResult['hits']), count($puceneResult['hits']));

        $elasticsearchHits = $this->normalize($elasticsearchResult['hits']);
        $puceneHits = $this->normalize($puceneResult['hits']);

        foreach ($puceneHits as $id => $puceneHit) {
            $this->assertArrayHasKey($id, $elasticsearchHits, $id);
            $this->assertEquals($elasticsearchHits[$id]['document'], $puceneHit['document']);

            // if position matches: OK
            // else score has to be equals
            if ($elasticsearchHits[$id]['position'] !== $puceneHit['position']) {
                $this->assertEquals($elasticsearchHits[$id]['_score'], $puceneHit['_score'], $id, 0.02);
            }
        }
    }
}
