<?php

namespace PimcoreContentMigration\DependencyInjection;

use Exception;

use function is_array;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class PimcoreContentMigrationExtension extends Extension
{
    /**
     * @param array<array<mixed>> $configs
     * @param ContainerBuilder $container
     * @return void
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Register templates as a parameter
        $value = [];
        if (isset($config['templates']) && is_array($config['templates'])) {
            $value = $config['templates'];
        }
        $container->setParameter('pimcore_content_migration.templates', $value);

        // Register default namespace as a parameter
        $defaultNamespace = null;
        if (isset($config['default_namespace']) && is_string($config['default_namespace'])) {
            $defaultNamespace = $config['default_namespace'];
        }
        $container->setParameter('pimcore_content_migration.default_namespace', $defaultNamespace);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');
    }
}
