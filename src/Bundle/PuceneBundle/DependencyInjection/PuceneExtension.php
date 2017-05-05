<?php

namespace Pucene\Bundle\PuceneBundle\DependencyInjection;

use Pucene\Component\Pucene\Dbal\DbalStorage;
use Pucene\Component\Symfony\Pool\CollectorCompilerPass;
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

        $adapter = $config['adapter'];
        $container->setParameter('pucene.adapter', $adapter);
        $container->setParameter('pucene.adapter_config.' . $adapter, $config['adapters'][$adapter]);
        $container->setParameter('pucene.indices', $config['indices']);

        $fileLocator = new FileLocator(__DIR__ . '/../Resources/config');

        $loaderResolver = new LoaderResolver(
            [
                new DirectoryLoader($container, $fileLocator),
                new XmlFileLoader($container, $fileLocator),
            ]
        );

        $loader = new DelegatingLoader($loaderResolver);
        $loader->import($fileLocator->locate($adapter . '/'));
        $loader->import($fileLocator->locate('commands.xml'));

        if ($adapter === 'pucene') {
            $this->loadPucene($config, $container);
        } elseif ($adapter === 'elasticsearch') {
            $this->loadElasticsearch($config, $container);
        }

        $container->setAlias('pucene.client', 'pucene.' . $adapter . '.client');
    }

    /**
     * Load specific configuration for pucene.
     *
     * @param array $config
     * @param ContainerBuilder $container
     */
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

            $serviceIds[$name] = 'pucene.pucene.doctrine_dbal.' . $name;
            $container->setDefinition($serviceIds[$name], $definition);
        }

        $container->getDefinition('pucene.pucene.storage_factory')->replaceArgument(1, $serviceIds);

        $pass = new CollectorCompilerPass('pucene.pucene.visitor', 'pucene.pucene.visitor_pool', 'query');
        $pass->process($container);

        $pass = new CollectorCompilerPass('pucene.pucene.interpreter', 'pucene.pucene.interpreter_pool', 'element');
        $pass->process($container);
    }

    /**
     * Load specific configuration for elasticsearch.
     *
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function loadElasticsearch($config, $container)
    {
        $pass = new CollectorCompilerPass('pucene.elasticsearch.visitor', 'pucene.elasticsearch.visitor_pool', 'query');
        $pass->process($container);
    }
}
