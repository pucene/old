<?php

namespace Pucene\Component\Pucene;

use Pucene\Component\Pucene\Model\Analysis;
use Pucene\Component\Pucene\Model\Document;
use Pucene\Component\QueryBuilder\Search;

interface StorageInterface
{
    public function exists(): bool;

    public function createIndex(array $parameters): void;

    public function deleteIndex(): void;

    public function saveDocument(Analysis $analysis): void;

    public function deleteDocument(string $id): void;

    /**
     * @return Document[]
     */
    public function search(Search $search, array $types): array;

    public function count(Search $search, array $types): int;

    public function get(?string $type, string $id): array;

    public function termStatistics(): TermStatisticsInterface;
}
