<?php

namespace Pucene\Component\Lucene;

use Pucene\Component\Client\IndexInterface;
use Pucene\Component\QueryBuilder\Search;

class PuceneIndex implements IndexInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function index(array $document, $type, $id = null)
    {
        // TODO index(array $document, $type, $id = null)
    }

    /**
     * {@inheritdoc}
     */
    public function delete($type, $id)
    {
        // TODO delete($type, $id)
    }

    /**
     * {@inheritdoc}
     */
    public function search(Search $search, $type)
    {
        // TODO search(Search $search, $type)
    }
}
