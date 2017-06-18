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
     * @param float $maxScore
     *
     * @return array
     */
    protected function normalize(array $hits, float $maxScore = null)
    {
        $result = [];
        $position = 0;
        foreach ($hits as $hit) {
            $score = $hit['_score'];
            unset($hit['_score']);
            unset($hit['sort']);
            $result[$hit['_id']] = [
                'position' => $position++,
                '_score' => $score,
                '_relativeScore' => null,
                'document' => $hit,
            ];

            if ($maxScore) {
                $result[$hit['_id']]['_relativeScore'] = $score / $maxScore;
            }
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
        // TODO total

        $elasticsearchHits = $this->normalize($elasticsearchResult['hits'], $elasticsearchResult['max_score']);
        $puceneHits = $this->normalize($puceneResult['hits'], $puceneResult['max_score']);

        foreach ($puceneHits as $id => $puceneHit) {
            $this->assertArrayHasKey($id, $elasticsearchHits, $id);
            $this->assertEquals($elasticsearchHits[$id]['document'], $puceneHit['document']);

            $this->assertEquals($elasticsearchHits[$id]['_relativeScore'], $puceneHit['_relativeScore'], '', 0.1);
        }
    }
}
