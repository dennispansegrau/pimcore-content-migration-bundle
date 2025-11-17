<?php

namespace PimcoreContentMigration\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class PimcoreContentMigrationExtension extends Extension
{
    /**
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Register templates as a parameter
        $container->setParameter('pimcore_content_migration.templates', $config['templates'] ?? []);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');
    }
}
