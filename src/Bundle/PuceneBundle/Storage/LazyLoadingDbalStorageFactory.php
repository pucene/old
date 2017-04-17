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

    /**
     * @param ContainerInterface $container
     * @param \string[] $serviceIds
     */
    public function __construct(ContainerInterface $container, array $serviceIds)
    {
        $this->container = $container;
        $this->serviceIds = $serviceIds;
    }

    /**
     * @param string $name
     *
     * @return StorageInterface
     */
    public function create(string $name)
    {
        return $this->container->get($this->serviceIds[$name]);
    }
}
