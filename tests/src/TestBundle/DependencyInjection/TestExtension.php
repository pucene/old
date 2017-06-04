<?php

namespace Pucene\Tests\TestBundle\DependencyInjection;

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

/**
 * TODO add description here.
 */
class TestExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('pucene.adapter_config.pucene', $config['adapters']['pucene']);
        $container->setParameter('pucene.adapter_config.elasticsearch', $config['adapters']['elasticsearch']);
        $container->setParameter('pucene.indices', $config['indices']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->import('commands.xml');

        $fileLocator = new FileLocator(__DIR__ . '/../../../../src/Bundle/PuceneBundle/Resources/config');
        $loaderResolver = new LoaderResolver(
            [new DirectoryLoader($container, $fileLocator), new XmlFileLoader($container, $fileLocator)]
        );

        $loader = new DelegatingLoader($loaderResolver);
        foreach (['pucene', 'elasticsearch', 'zend_search'] as $adapter) {
            $loader->import($fileLocator->locate($adapter . '/'));
        }

        $this->loadPucene($config, $container);
        $this->loadZendSearch($config, $container);
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
                    new Reference('pucene.pucene.compiler'),
                    new Reference('pucene.pucene.interpreter'),
                ]
            );

            $container->setDefinition('pucene.pucene.doctrine_dbal.' . $name, $definition);
            $serviceIds[$name] = 'pucene.pucene.doctrine_dbal.' . $name;
        }

        $container->getDefinition('pucene.pucene.storage_factory')->replaceArgument(1, $serviceIds);
    }

    private function loadZendSearch(array $config, ContainerBuilder $container)
    {
        $container->setParameter(
            'pucene.adapter_config.zend_search.directory',
            $config['adapters']['zend_search']['directory']
        );
    }
}
