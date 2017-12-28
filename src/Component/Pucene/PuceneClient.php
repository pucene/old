<?php

namespace Pucene\Component\Pucene;

use Pucene\Component\Client\ClientInterface;
use Pucene\Component\Client\IndexInterface;
use Pucene\Component\Pucene\Mapping\Mapping;

class PuceneClient implements ClientInterface
{
    /**
     * @var StorageFactoryInterface
     */
    private $storageFactory;

    /**
     * @var Mapping
     */
    private $mapping;

    public function __construct(StorageFactoryInterface $storageFactory, Mapping $mapping)
    {
        $this->storageFactory = $storageFactory;
        $this->mapping = $mapping;
    }

    public function exists(string $name): bool
    {
        return $this->storageFactory->create($name)->exists();
    }

    public function get(string $name): IndexInterface
    {
        return new PuceneIndex($name, $this->storageFactory->create($name), $this->mapping);
    }

    public function create(string $name, array $parameters): IndexInterface
    {
        if ($this->exists($name)) {
            throw new \Exception('Index already exists');
        }

        $this->storageFactory->create($name)->createIndex($parameters);

        return $this->get($name);
    }

    public function delete(string $name): void
    {
        $this->storageFactory->create($name)->deleteIndex();
    }
}
