<?php

namespace Pucene\InvertedIndex;

use Pucene\Analysis\Token;
use Pucene\Component\QueryBuilder\Search;

interface StorageInterface
{
    public function beginSaveDocument();

    public function save(Token $token, array $document, $fieldName);

    public function finishSaveDocument();

    public function search(Search $search);

    public function remove($id);
}
