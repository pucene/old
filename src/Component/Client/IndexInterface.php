<?php

namespace Pucene\Component\Client;

use Pucene\Component\QueryBuilder\Search;

interface IndexInterface
{
    public function index(array $document, string $type, ?string $id = null): array;

    public function delete(string $type, string $id): void;

    /**
     * @param string|string[] $type
     */
    public function search(Search $search, $type): array;

    public function count(Search $search, $type): int;

    public function get(?string $type, string $id): array;
}
