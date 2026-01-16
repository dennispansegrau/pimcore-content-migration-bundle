<?php

namespace PimcoreContentMigration;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use PimcoreContentMigration\DependencyInjection\Compiler\ValueStringifierCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class PimcoreContentMigrationBundle extends AbstractPimcoreBundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new ValueStringifierCompilerPass());
    }
}
