<?php

namespace Pucene\Tests\src\Functional\Comparison;

use Pucene\Component\Client\IndexInterface;
use Pucene\Component\QueryBuilder\Query\FullText\Match;
use Pucene\Component\QueryBuilder\Query\MatchAll;
use Pucene\Component\QueryBuilder\Query\TermLevel\Term;
use Pucene\Component\QueryBuilder\Search;
use Pucene\Component\QueryBuilder\Sort\IdSort;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * This testcase compares elasticsearch with pucene results for different queries.
 */
class SearchComparisonTest extends KernelTestCase
{
    /**
     * @var IndexInterface
     */
    private $puceneIndex;

    /**
     * @var IndexInterface
     */
    private $elasticsearchIndex;

    protected function setUp()
    {
        $this->bootKernel();

        $pucene = $this->get('pucene.pucene.client');
        $this->puceneIndex = $pucene->get('my_index');

        $elasticsearch = $this->get('pucene.elasticsearch.client');
        $this->elasticsearchIndex = $elasticsearch->get('my_index');
    }

    public function testSearchTerm()
    {
        $search = new Search(new Term('title', 'museum'));

        $elasticsearchResult = $this->elasticsearchIndex->search($search, 'my_type');
        $puceneResult = $this->puceneIndex->search($search, 'my_type');

        $this->assertEquals(count($elasticsearchResult['hits']), count($puceneResult['hits']));

        $elasticsearchHits = $this->normalize($elasticsearchResult['hits']);
        $puceneHits = $this->normalize($puceneResult['hits']);

        foreach ($puceneHits as $id => $puceneHit) {
            $this->assertEquals($elasticsearchHits[$id]['document'], $puceneHit['document']);

            // if position matches: OK
            // else score has to be equals
            if ($elasticsearchHits[$id]['position'] !== $puceneHit['position']) {
                $this->assertEquals($elasticsearchHits[$id]['_score'], $puceneHit['_score'], '', 0.001);
            }
        }
    }

    public function testSearchMatch()
    {
        $search = new Search(new Match('title', 'museum lyon'));
        $search->setSize(20);

        $elasticsearchResult = $this->elasticsearchIndex->search($search, 'my_type');
        $puceneResult = $this->puceneIndex->search($search, 'my_type');

        $this->assertEquals(count($elasticsearchResult['hits']), count($puceneResult['hits']));

        $elasticsearchHits = $this->normalize($elasticsearchResult['hits']);
        $puceneHits = $this->normalize($puceneResult['hits']);

        foreach ($puceneHits as $id => $puceneHit) {
            $this->assertEquals($elasticsearchHits[$id]['document'], $puceneHit['document']);

            // if position matches: OK
            // else score has to be equals
            if ($elasticsearchHits[$id]['position'] !== $puceneHit['position']) {
                $this->assertEquals($elasticsearchHits[$id]['_score'], $puceneHit['_score'], $id, 0.001);
            }
        }
    }

    public function testSearchMatchAll()
    {
        $search = new Search(new MatchAll());
        $search->addSort(new IdSort());

        $elasticsearchResult = $this->elasticsearchIndex->search($search, 'my_type');
        $puceneResult = $this->puceneIndex->search($search, 'my_type');

        $this->assertEquals(count($elasticsearchResult['hits']), count($puceneResult['hits']));

        $elasticsearchHits = $this->normalize($elasticsearchResult['hits']);
        $puceneHits = $this->normalize($puceneResult['hits']);

        foreach ($puceneHits as $id => $puceneHit) {
            $this->assertEquals($elasticsearchHits[$id]['document'], $puceneHit['document']);
            $this->assertEquals($elasticsearchHits[$id]['position'], $puceneHit['position']);
        }
    }

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

    protected function get($id)
    {
        return self::$kernel->getContainer()->get($id);
    }
}
