<?php

namespace DennisPansegrau\PimcoreContentMigrationBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class PimcoreContentMigrationBundle extends AbstractPimcoreBundle
{
    /**
     * @throws \Exception
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../config'));
        $loader->load('services.yaml');
    }
}
