<?php

namespace Pucene\InvertedIndex;

use Pucene\Analysis\Token;
use Pucene\Component\QueryBuilder\Search;

class InvertedIndex
{
    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param array $document
     * @param string $fieldName
     * @param Token[] $tokens
     */
    public function save(array $document, $fieldName, array $tokens)
    {
        foreach ($tokens as $token) {
            $this->storage->save($token, $document, $fieldName);
        }
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
}
