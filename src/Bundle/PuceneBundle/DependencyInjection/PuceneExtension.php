<?php

namespace Pucene\Bundle\PuceneBundle\DependencyInjection;

use Pucene\Component\Pucene\Dbal\DbalStorage;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\DirectoryLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class PuceneExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('pucene.adapter', $config['adapter']);
        $container->setParameter('pucene.adapter_config', $config['adapters'][$config['adapter']]);
        $container->setParameter('pucene.indices', $config['indices']);

        $fileLocator = new FileLocator(__DIR__ . '/../Resources/config');

        $loaderResolver = new LoaderResolver(
            [new DirectoryLoader($container, $fileLocator), new XmlFileLoader($container, $fileLocator)]
        );

        $loader = new DelegatingLoader($loaderResolver);
        $loader->import($fileLocator->locate($config['adapter'] . '/'));
        $loader->import($fileLocator->locate('commands.xml'));

        if ($config['adapter'] === 'pucene') {
            $this->loadPucene($config, $container);
        }
    }

    private function loadPucene(array $config, ContainerBuilder $container)
    {
        $serviceIds = [];
        foreach ($config['indices'] as $name => $options) {
            $definition = new Definition(
                DbalStorage::class,
                [
                    $name,
                    new Reference($config['adapters']['pucene']['doctrine_dbal_connection']),
                    new Reference('pucene.pucene.query_builder.search'),
                ]
            );

            $container->setDefinition('pucene.pucene.doctrine_dbal.' . $name, $definition);
            $serviceIds[$name] = 'pucene.pucene.doctrine_dbal.' . $name;
        }

        $container->getDefinition('pucene.storage_factory')->replaceArgument(1, $serviceIds);
    }
}
