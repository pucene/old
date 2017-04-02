<?php

namespace Pucene\Component\Pucene;

use Pucene\Component\Analysis\AnalyzerInterface;
use Pucene\Component\Client\ClientInterface;

class PuceneClient implements ClientInterface
{
    /**
     * @var StorageInterface[]
     */
    private $storages;

    /**
     * @var AnalyzerInterface
     */
    private $analyzer;

    /**
     * @param StorageInterface[] $storages
     * @param AnalyzerInterface $analyzer
     */
    public function __construct(array $storages, AnalyzerInterface $analyzer)
    {
        $this->storages = $storages;
        $this->analyzer = $analyzer;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        return new PuceneIndex($name, $this->storages[$name], $this->analyzer);
    }

    /**
     * {@inheritdoc}
     */
    public function create($name, array $parameters)
    {
        $this->storages[$name]->createIndex($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($name)
    {
        $this->storages[$name]->deleteIndex();
    }
}
