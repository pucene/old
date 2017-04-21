<?php

namespace Pucene\Component\Pucene;

use Pucene\Component\Analysis\AnalyzerInterface;
use Pucene\Component\Client\ClientInterface;

class PuceneClient implements ClientInterface
{
    /**
     * @var StorageFactoryInterface
     */
    private $storageFactory;

    /**
     * @var AnalyzerInterface
     */
    private $analyzer;

    /**
     * @param StorageFactoryInterface $storageFactory
     * @param AnalyzerInterface $analyzer
     */
    public function __construct(StorageFactoryInterface $storageFactory, AnalyzerInterface $analyzer)
    {
        $this->storageFactory = $storageFactory;
        $this->analyzer = $analyzer;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        return new PuceneIndex($name, $this->storageFactory->create($name), $this->analyzer);
    }

    /**
     * {@inheritdoc}
     */
    public function create($name, array $parameters)
    {
        return $this->storageFactory->create($name)->createIndex($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($name)
    {
        return $this->storageFactory->create($name)->deleteIndex();
    }
}
