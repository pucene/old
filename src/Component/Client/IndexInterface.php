<?php

namespace Pucene\Component\Client;

use Pucene\Component\QueryBuilder\Search;

interface IndexInterface
{
    /**
     * @param array $document
     * @param string $type
     * @param string|null $id
     *
     * @return array
     */
    public function index(array $document, $type, $id = null);

    /**
     * @param string $type
     * @param string $id
     */
    public function delete($type, $id);

    /**
     * @param Search $search
     * @param string|string[] $type
     *
     * @return array
     */
    public function search(Search $search, $type);
}
