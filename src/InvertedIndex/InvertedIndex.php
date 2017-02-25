<?php

namespace Pucene\InvertedIndex;

use Pucene\Analysis\Token;

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
     * @param Token[] $tokens
     */
    public function save(array $document, array $tokens)
    {
        foreach ($tokens as $token) {
            $this->storage->save($token, $document);
        }
    }

    /**
     * @param Token $token
     *
     * @return array[]
     */
    public function search(Token $token)
    {
        return $this->storage->getDocuments($token);
    }
}
