<?php

namespace Pucene\Tests\Functional\Comparison;

use Pucene\Component\QueryBuilder\Query\FullText\Match;
use Pucene\Component\QueryBuilder\Query\MatchAll;
use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\DocumentLike;
use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\MoreLikeThis;
use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\TextLike;
use Pucene\Component\QueryBuilder\Query\TermLevel\Term;
use Pucene\Component\QueryBuilder\Search;
use Pucene\Component\QueryBuilder\Sort\IdSort;

/**
 * This testcase compares elasticsearch with pucene results for different queries.
 */
class SearchComparisonTest extends ComparisonTestCase
{
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
                $this->assertEquals($elasticsearchHits[$id]['_score'], $puceneHit['_score'], '', 0.002);
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
                $this->assertEquals($elasticsearchHits[$id]['_score'], $puceneHit['_score'], $id, 0.002);
            }
        }
    }

    public function testSearchMoreLikeThisText()
    {
        $query = new MoreLikeThis([new TextLike('Museum of Arts of Lyon')], ['title']);
        $query->setMinTermFrequency(1);

        $search = new Search($query);
        $search->setSize(500);

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
                $this->assertEquals($elasticsearchHits[$id]['_score'], $puceneHit['_score'], $id, 0.002);
            }
        }
    }

    public function testSearchMoreLikeThisDocument()
    {
        $query = new MoreLikeThis([new DocumentLike('my_index', 'my_type', 'Q4872')], ['title']);
        $query->setMinTermFrequency(1);

        $search = new Search($query);
        $search->setSize(500);

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
                $this->assertEquals($elasticsearchHits[$id]['_score'], $puceneHit['_score'], $id, 0.002);
            }
        }
    }

    public function testSearchMoreLikeThisDocuments()
    {
        $query = new MoreLikeThis(
            [new DocumentLike('my_index', 'my_type', 'Q435'), new DocumentLike('my_index', 'my_type', 'Q4872')],
            ['title']
        );
        $query->setMinTermFrequency(1);

        $search = new Search($query);
        $search->setSize(500);

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
                $this->assertEquals($elasticsearchHits[$id]['_score'], $puceneHit['_score'], $id, 0.002);
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
}
