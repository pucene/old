<?php

namespace Pucene\Bundle\PuceneBundle\Pucene;

use Pucene\Component\Pucene\Dbal\QueryBuilder\QueryBuilderFactoryPoolInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LazyLoadingQueryBuilderFactoryPool implements QueryBuilderFactoryPoolInterface
{
    /**
     * @var string[]
     */
    private $serviceIds;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param \string[] $serviceIds
     * @param ContainerInterface $container
     */
    public function __construct(array $serviceIds, ContainerInterface $container)
    {
        $this->serviceIds = $serviceIds;
        $this->container = $container;
    }

    public function get($className)
    {
        return $this->container->get($this->serviceIds[$className]);
    }
}
