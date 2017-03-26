<?php

namespace Pucene\Index;

use Pucene\Component\QueryBuilder\Search;
use Pucene\InvertedIndex\InvertedIndex;
use Ramsey\Uuid\Uuid;

class Index
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var InvertedIndex
     */
    protected $invertedIndex;

    /**
     * @param InvertedIndex $invertedIndex
     * @param string $name
     */
    public function __construct(InvertedIndex $invertedIndex, $name)
    {
        $this->invertedIndex = $invertedIndex;
        $this->name = $name;
    }

    public function index(array $document, $id = null)
    {
        if ($id) {
            $this->remove($id);
        }

        $document = [
            '_id' => $id ?: Uuid::uuid4()->toString(),
            '_type' => 'default', // FIXME
            '_index' => $this->name,
            '_source' => $document,
        ];

        $this->invertedIndex->save($document);

        return $document;
    }

    public function remove($id)
    {
        $this->invertedIndex->remove($id);
    }

    public function search(Search $search)
    {
        return $this->invertedIndex->search($search);
    }
}
