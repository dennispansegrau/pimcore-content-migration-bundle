<?php

namespace DennisPansegrau\PimcoreContentMigrationBundle\DependencyInjection;

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
                'document_template' => '@PimcoreContentMigration/code_templates/document.twig',
                'asset_template' => '@PimcoreContentMigration/code_templates/asset.twig',
                'object_template' => '@PimcoreContentMigration/code_templates/object.twig',
            ])
            ->end()
            ->end();

        return $treeBuilder;
    }
}
