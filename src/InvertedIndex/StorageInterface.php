<?php

namespace Pucene\InvertedIndex;

use Pucene\Analysis\Token;
use Pucene\Component\QueryBuilder\Search;

interface StorageInterface
{
    public function save(Token $token, array $document, $fieldName);

    public function search(Search $search);
}
