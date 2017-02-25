<?php

namespace Pucene\InvertedIndex;

use Pucene\Analysis\Token;

class ArrayStorage implements StorageInterface
{
    /**
     * @var array[][]
     */
    private $invertedIndex = [];

    public function save(Token $token, array $document)
    {
        if (!array_key_exists($token->getToken(), $this->invertedIndex)) {
            $this->invertedIndex[$token->getToken()] = [];
        }

        $this->invertedIndex[$token->getToken()][] = $document;
    }

    public function getDocuments(Token $token)
    {
        return $this->invertedIndex[$token->getToken()];
    }
}
