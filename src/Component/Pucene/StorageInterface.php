<?php

namespace Pucene\Component\Pucene;

use Pucene\Component\Pucene\Model\Analysis;

interface StorageInterface
{
    public function createIndex(array $parameters);

    public function deleteIndex();

    public function saveDocument(Analysis $analysis);

    public function deleteDocument($id);
}
