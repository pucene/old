<?php

namespace Pucene\Bundle\PuceneBundle\Storage;

use Pucene\Component\Pucene\StorageFactoryInterface;
use Pucene\Component\Pucene\StorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LazyLoadingDbalStorageFactory implements StorageFactoryInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string[]
     */
    private $serviceIds;

    public function __construct(ContainerInterface $container, array $serviceIds)
    {
        $this->container = $container;
        $this->serviceIds = $serviceIds;
    }

    public function create(string $name): StorageInterface
    {
        return $this->container->get($this->serviceIds[$name]);
    }
}
