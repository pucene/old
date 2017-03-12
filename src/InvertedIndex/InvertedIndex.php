<?php

namespace Pucene\InvertedIndex;

use Pucene\Analysis\AnalyzerInterface;
use Pucene\Component\QueryBuilder\Search;

class InvertedIndex
{
    /**
     * @var AnalyzerInterface
     */
    private $analyzer;

    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @param AnalyzerInterface $analyzer
     * @param StorageInterface $storage
     */
    public function __construct(AnalyzerInterface $analyzer, StorageInterface $storage)
    {
        $this->analyzer = $analyzer;
        $this->storage = $storage;
    }

    /**
     * @param array $document
     */
    public function save(array $document)
    {
        $this->storage->beginSaveDocument();
        foreach ($document['_source'] as $fieldName => $fieldContent) {
            $this->saveTokens($document, $fieldName, $this->analyzer->analyze($fieldContent));
        }

        $this->storage->finishSaveDocument();
    }

    private function saveTokens(array $document, $fieldName, array $tokens)
    {
        $this->storage->beginSaveDocument();
        foreach ($tokens as $token) {
            $this->storage->save($token, $document, $fieldName);
        }

        $this->storage->finishSaveDocument();
    }

    /**
     * @param Search $search
     *
     * @return array[]
     */
    public function search(Search $search)
    {
        return $this->storage->search($search);
    }

    public function remove($id)
    {
        $this->storage->remove($id);
    }
}
