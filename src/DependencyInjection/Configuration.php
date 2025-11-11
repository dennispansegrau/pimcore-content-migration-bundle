<?php

namespace PimcoreContentMigration\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('pimcore_content_migration');

        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
            ->arrayNode('templates')
            ->info('Mapping of template keys to Twig template paths.')
            ->useAttributeAsKey('name')
            ->scalarPrototype()->end()
            ->defaultValue([
                'migration_template' => '@PimcoreContentMigration/code_templates/migration.php.twig',
                'document_template' => '@PimcoreContentMigration/code_templates/document.php.twig',
                'asset_template' => '@PimcoreContentMigration/code_templates/asset.php.twig',
                'object_template' => '@PimcoreContentMigration/code_templates/object.php.twig',
            ])
            ->end()
            ->end();

        return $treeBuilder;
    }
}
