<?php

namespace Pucene\Tests\src\Functional\Comparison;

use Pucene\Component\Client\ClientInterface;
use Pucene\Component\QueryBuilder\Query\FullText\Match;
use Pucene\Component\QueryBuilder\Query\MatchAll;
use Pucene\Component\QueryBuilder\Query\TermLevel\Term;
use Pucene\Component\QueryBuilder\Search;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * This testcase compares elasticsearch with pucene results for different queries.
 */
class SearchComparisonTest extends KernelTestCase
{
    protected function get($id)
    {
        return self::$kernel->getContainer()->get($id);
    }

    public function testSearchTerm()
    {
        $this->bootKernel();

        /** @var ClientInterface $pucene */
        $pucene = $this->get('pucene.pucene.client');
        $puceneIndex = $pucene->get('my_index');

        /** @var ClientInterface $elasticsearch */
        $elasticsearch = $this->get('pucene.pucene.client');
        $elasticsearchIndex = $elasticsearch->get('my_index');

        $search = new Search(new Term('title', 'museum'));

        $elasticsearchResult = $elasticsearchIndex->search($search, 'my_type');
        $puceneResult = $puceneIndex->search($search, 'my_type');

        $this->assertEquals(count($elasticsearchResult['hits']), count($puceneResult['hits']));

        for ($index = 0, $length = count($puceneResult['hits']); $index < $length; ++$index) {
            $this->assertEquals($elasticsearchResult['hits'][$index], $puceneResult['hits'][$index]);
        }
    }

    public function testSearchMatch()
    {
        $this->bootKernel();

        /** @var ClientInterface $pucene */
        $pucene = $this->get('pucene.pucene.client');
        $puceneIndex = $pucene->get('my_index');

        /** @var ClientInterface $elasticsearch */
        $elasticsearch = $this->get('pucene.pucene.client');
        $elasticsearchIndex = $elasticsearch->get('my_index');

        $search = new Search(new Match('title', 'museum lyon'));

        $elasticsearchResult = $elasticsearchIndex->search($search, 'my_type');
        $puceneResult = $puceneIndex->search($search, 'my_type');

        $this->assertEquals(count($elasticsearchResult['hits']), count($puceneResult['hits']));

        for ($index = 0, $length = count($puceneResult['hits']); $index < $length; ++$index) {
            $this->assertEquals($elasticsearchResult['hits'][$index], $puceneResult['hits'][$index]);
        }
    }

    public function testSearchMatchAll()
    {
        $this->bootKernel();

        /** @var ClientInterface $pucene */
        $pucene = $this->get('pucene.pucene.client');
        $puceneIndex = $pucene->get('my_index');

        /** @var ClientInterface $elasticsearch */
        $elasticsearch = $this->get('pucene.pucene.client');
        $elasticsearchIndex = $elasticsearch->get('my_index');

        $search = new Search(new MatchAll());

        $elasticsearchResult = $elasticsearchIndex->search($search, 'my_type');
        $puceneResult = $puceneIndex->search($search, 'my_type');

        $this->assertEquals(count($elasticsearchResult['hits']), count($puceneResult['hits']));

        for ($index = 0, $length = count($puceneResult['hits']); $index < $length; ++$index) {
            $this->assertEquals($elasticsearchResult['hits'][$index], $puceneResult['hits'][$index]);
        }
    }
}
