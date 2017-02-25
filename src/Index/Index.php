<?php

namespace Pucene\Index;

use Pucene\Analysis\AnalyzerInterface;
use Pucene\InvertedIndex\InvertedIndex;
use Ramsey\Uuid\Uuid;

class Index
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var AnalyzerInterface
     */
    protected $analyzer;

    /**
     * @var InvertedIndex
     */
    protected $invertedIndex;

    /**
     * @param AnalyzerInterface $analyzer
     * @param InvertedIndex $invertedIndex
     * @param string $name
     */
    public function __construct(AnalyzerInterface $analyzer, InvertedIndex $invertedIndex, $name)
    {
        $this->analyzer = $analyzer;
        $this->invertedIndex = $invertedIndex;
        $this->name = $name;
    }


    public function index(array $document, $id = null)
    {
        if ($id) {
            $this->deindex($id);
        }

        $document = [
            '_id' => $id ?: Uuid::uuid4()->toString(),
            '_type' => 'default', // FIXME
            '_index' => $this->name,
            '_source' => $document,
        ];

        foreach ($document['_source'] as $fieldName => $fieldContent) {
            $this->invertedIndex->save($document, $this->analyzer->analyze($fieldContent));
        }

        return $document;
    }

    private function deindex($id)
    {
        // TODO implement deindex($id)
    }

    public function search($query)
    {
        $result = [];
        $tokens = $this->analyzer->analyze($query);
        foreach ($tokens as $token) {
            $documents = $this->invertedIndex->search($token);
            $result = array_merge($result, $documents);
        }

        return $result;
    }
}
