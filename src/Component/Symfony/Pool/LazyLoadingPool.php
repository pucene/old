<?php

namespace Pucene\Component\Symfony\Pool;

use Symfony\Component\DependencyInjection\ContainerInterface;

class LazyLoadingPool implements PoolInterface
{
    /**
     * @var string[]
     */
    private $serviceIds;

    /**
     * @var ContainerInterface
     */
    private $serviceContainer;

    /**
     * @param string[] $serviceIds
     * @param ContainerInterface $serviceContainer
     */
    public function __construct(array $serviceIds, ContainerInterface $serviceContainer)
    {
        $this->serviceIds = $serviceIds;
        $this->serviceContainer = $serviceContainer;
    }

    public function get($alias)
    {
        return $this->serviceContainer->get($this->serviceIds[$alias]);
    }
}
