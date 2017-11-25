<?php

namespace Pucene\Component\Pucene;

use Pucene\Component\Analysis\AnalyzerInterface;
use Pucene\Component\Client\ClientInterface;
use Pucene\Component\Client\IndexInterface;

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

    public function __construct(StorageFactoryInterface $storageFactory, AnalyzerInterface $analyzer)
    {
        $this->storageFactory = $storageFactory;
        $this->analyzer = $analyzer;
    }

    public function get(string $name): IndexInterface
    {
        return new PuceneIndex($name, $this->storageFactory->create($name), $this->analyzer);
    }

    public function create(string $name, array $parameters): IndexInterface
    {
        $this->storageFactory->create($name)->createIndex($parameters);

        return $this->get($name);
    }

    public function delete(string $name): void
    {
        $this->storageFactory->create($name)->deleteIndex();
    }
}
