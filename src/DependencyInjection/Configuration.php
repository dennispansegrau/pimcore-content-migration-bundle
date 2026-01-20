<?php

namespace PimcoreContentMigration\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('pimcore_content_migration', 'array');

        $rootNode = $treeBuilder->getRootNode();
        if (!$rootNode instanceof ArrayNodeDefinition) {
            return $treeBuilder;
        }

        $childrenNode = $rootNode->children();
        $templatesNode = $childrenNode
            ->arrayNode('templates')
            ->info('Mapping of template keys to Twig template paths.')
            ->useAttributeAsKey('name');
        $prototypeNode = $templatesNode->scalarPrototype();
        $prototypeNode->end();
        $templatesNode->defaultValue([
            'migration_template' => '@PimcoreContentMigration/code_templates/migration.php.twig',
            'document_template' => '@PimcoreContentMigration/code_templates/document.php.twig',
            'asset_template' => '@PimcoreContentMigration/code_templates/asset.php.twig',
            'object_template' => '@PimcoreContentMigration/code_templates/object.php.twig',
        ]);
        $templatesNode->end();
        $childrenNode
            ->scalarNode('default_namespace')
            ->defaultValue(null)
            ->cannotBeEmpty();
        $childrenNode->end();

        return $treeBuilder;
    }
}
