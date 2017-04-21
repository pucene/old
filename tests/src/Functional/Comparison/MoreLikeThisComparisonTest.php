<?php

namespace Pucene\Tests\Functional\Comparison;

use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\ArtificialDocumentLike;
use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\DocumentLike;
use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\MoreLikeThis;
use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\TextLike;
use Pucene\Component\QueryBuilder\Search;

/**
 * This testcase compares elasticsearch with pucene results for the "more_like_this" query.
 */
class MoreLikeThisComparisonTest extends ComparisonTestCase
{
    public function testText()
    {
        $query = new MoreLikeThis([new TextLike('Museum of Arts of Lyon')], ['title']);
        $query->setMinTermFreq(1);

        $search = new Search($query);
        $search->setSize(500);

        $this->assertSearch($search);
    }

    public function testDocument()
    {
        $query = new MoreLikeThis([new DocumentLike('my_index', 'my_type', 'Q4872')], ['title']);
        $query->setMinTermFreq(1);

        $search = new Search($query);
        $search->setSize(500);

        $this->assertSearch($search);
    }

    public function testDocuments()
    {
        $query = new MoreLikeThis(
            [new DocumentLike('my_index', 'my_type', 'Q435'), new DocumentLike('my_index', 'my_type', 'Q4872')],
            ['title']
        );
        $query->setMinTermFreq(1);

        $search = new Search($query);
        $search->setSize(500);

        $this->assertSearch($search);
    }

    public function testArtificialDocument()
    {
        $query = new MoreLikeThis(
            [
                new ArtificialDocumentLike(
                    'my_index',
                    'my_type',
                    [
                        'title' => 'Pushkin Museum of Fine Arts',
                    ]
                ),
            ],
            ['title']
        );
        $query->setMinTermFreq(1);

        $search = new Search($query);
        $search->setSize(500);

        $this->assertSearch($search);
    }

    public function testArtificialDocuments()
    {
        $query = new MoreLikeThis(
            [
                new ArtificialDocumentLike(
                    'my_index',
                    'my_type',
                    [
                        'title' => 'Library of Alexandria',
                    ]
                ),
                new ArtificialDocumentLike(
                    'my_index',
                    'my_type',
                    [
                        'title' => 'Pushkin Museum of Fine Arts',
                    ]
                ),
            ],
            ['title']
        );
        $query->setMinTermFreq(1);

        $search = new Search($query);
        $search->setSize(500);

        $this->assertSearch($search);
    }

    public function testMaxQueryTerms()
    {
        $query = new MoreLikeThis(
            [new DocumentLike('my_index', 'my_type', 'Q435'), new DocumentLike('my_index', 'my_type', 'Q4872')],
            ['title', 'description']
        );
        $query->setMinTermFreq(1);
        $query->setMaxQueryTerms(5);

        $search = new Search($query);
        $search->setSize(500);

        $this->assertSearch($search);
    }
}
