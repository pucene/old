<?php

namespace Pucene\Component\Pucene;

use Pucene\Component\Pucene\Model\Analysis;
use Pucene\Component\QueryBuilder\Search;

interface StorageInterface
{
    public function createIndex(array $parameters);

    public function deleteIndex();

    public function saveDocument(Analysis $analysis);

    public function deleteDocument($id);

    public function search(Search $search, $type);

    public function get($type, $id);

    public function termStatistics();

    public function optimize();
}
