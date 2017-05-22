<?php

namespace Pucene\Component\ZendSearch;

use Pucene\Component\Client\IndexInterface;
use Pucene\Component\QueryBuilder\Search;
use Ramsey\Uuid\Uuid;
use ZendSearch\Lucene\Analysis\Analyzer\Analyzer;
use ZendSearch\Lucene\Analysis\Analyzer\Common\Utf8\CaseInsensitive;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Index;
use ZendSearch\Lucene\Search\Query\Term;
use ZendSearch\Lucene\Search\QueryHit;
use ZendSearch\Lucene\Search\QueryParser;

class ZendSearchIndex implements IndexInterface
{
    const ID_FIELD = '_id';
    const TYPE_FIELD = '_type';
    const SOURCE_FIELD = '_source';

    /**
     * @var string
     */
    private $name;

    /**
     * @var Index
     */
    private $index;

    /**
     * @param string $name
     * @param Index $index
     */
    public function __construct(string $name, Index $index)
    {
        $this->name = $name;
        $this->index = $index;

        QueryParser::setDefaultOperator(QueryParser::B_AND);
        Analyzer::setDefault(new CaseInsensitive());
    }

    /**
     * {@inheritdoc}
     */
    public function index(array $document, $type, $id = null)
    {
        if ($id) {
            $this->delete($type, $id);
        }

        $zendDocument = new Document();
        $zendDocument->addField(Document\Field::keyword(self::ID_FIELD, $id ?: Uuid::uuid4()->toString()));
        $zendDocument->addField(Document\Field::text(self::TYPE_FIELD, $type));
        $zendDocument->addField(Document\Field::unIndexed(self::SOURCE_FIELD, serialize($document)));

        foreach ($document as $key => $value) {
            $zendDocument->addField(Document\Field::text($key, $value));
        }

        $this->index->addDocument($zendDocument);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($type, $id)
    {
        $hit = $this->getHit($type, $id);
        if (!$hit) {
            return;
        }

        $this->index->delete($hit->id);
    }

    /**
     * {@inheritdoc}
     */
    public function search(Search $search, $type)
    {
        $hits = $this->index->find(
            new Term(new Index\Term($search->getQuery()->getTerm(), $search->getQuery()->getField()))
        );

        $documents = [];
        foreach ($hits as $hit) {
            /** @var Document $document */
            $document = $hit->getDocument();
            $documents[] = [
                '_id' => $document->getFieldValue(self::ID_FIELD),
                '_type' => $document->getFieldValue(self::TYPE_FIELD),
                '_index' => $this->name,
                '_score' => $hit->score,
                '_source' => unserialize($document->getFieldValue(self::SOURCE_FIELD)),
            ];
        }

        return ['hits' => $documents];
    }

    /**
     * {@inheritdoc}
     */
    public function get($type, $id)
    {
        $document = $this->getHit($type, $id)->getDocument();
        if (!$document) {
            return;
        }

        return unserialize($document->getFieldValue(self::SOURCE_FIELD));
    }

    /**
     * @param string $type
     * @param string $id
     *
     * @return QueryHit
     */
    private function getHit(string $type, string $id)
    {
        $hits = $this->index->find(self::ID_FIELD . ':' . $id . ' AND ' . self::TYPE_FIELD . ':' . $type);
        if (count($hits) === 0) {
            return;
        }

        return $hits[0];
    }
}
