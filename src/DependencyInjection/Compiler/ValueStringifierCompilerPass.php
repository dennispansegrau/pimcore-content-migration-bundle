<?php

namespace PimcoreContentMigration\DependencyInjection\Compiler;

use PimcoreContentMigration\Converter\Stringifier\ValueToStringConverter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ValueStringifierCompilerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(ValueToStringConverter::class)) {
            return;
        }

        $definition = $container->findDefinition(ValueToStringConverter::class);

        $services = $this->findAndSortTaggedServices('pcmb.value_stringifier', $container);
        $definition->setArgument('$handlers', $services);
    }
}
