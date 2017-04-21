<?php

namespace Pucene\Bundle\PuceneBundle\Pucene;

use Pucene\Component\Pucene\Dbal\QueryBuilder\QueryBuilderPoolInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LazyLoadingQueryBuilderPool implements QueryBuilderPoolInterface
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
