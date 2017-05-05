<?php

namespace Pucene\Component\Pucene\Dbal;

use Doctrine\DBAL\Connection;
use Pucene\Component\Pucene\StorageFactoryInterface;
use Pucene\Component\Pucene\StorageInterface;

class DbalStorageFactory implements StorageFactoryInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var SearchBuilder
     */
    private $searchBuilder;

    /**
     * @var StorageInterface[]
     */
    private $storages = [];

    /**
     * @param Connection $connection
     * @param SearchBuilder $searchBuilder
     */
    public function __construct(Connection $connection, SearchBuilder $searchBuilder)
    {
        $this->connection = $connection;
        $this->searchBuilder = $searchBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $name)
    {
        if ($this->storages[$name]) {
            return $this->storages[$name];
        }

        return $this->storages[$name] = new DbalStorage($name, $this->connection, $this->searchBuilder);
    }
}
