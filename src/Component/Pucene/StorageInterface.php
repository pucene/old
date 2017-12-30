<?php

namespace Pucene\Component\Pucene;

use Pucene\Component\Pucene\Model\Analysis;
use Pucene\Component\QueryBuilder\Search;

interface StorageInterface
{
    public function exists(): bool;

    public function createIndex(array $parameters): void;

    public function deleteIndex(): void;

    public function saveDocument(Analysis $analysis): void;

    public function deleteDocument(string $id): void;

    public function search(Search $search, array $types): array;

    public function get(?string $type, string $id): array;

    public function termStatistics(): TermStatisticsInterface;
}
