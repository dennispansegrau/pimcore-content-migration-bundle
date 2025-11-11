<?php

namespace PimcoreContentMigration\Generator;

use Pimcore\Model\Element\AbstractElement;
use PimcoreContentMigration\Converter\AbstractElementToMethodNameConverter;
use PimcoreContentMigration\Writer\NamespaceResolver;

readonly class MigrationGenerator
{
    private const PREFIX = 'Version';

    public function __construct(
        private CodeGenerator $codeGenerator,
        private NamespaceResolver $namespaceResolver,
        private AbstractElementToMethodNameConverter $methodNameConverter,
    ) {
    }

    public function generateMigrationFile(AbstractElement $object, string $methodCode, Settings $settings): string
    {
        $namespace = $settings->getNamespace();
        $classname = self::PREFIX . date('YmdHis');
        $filename = $classname . '.php';
        $path = $this->namespaceResolver->resolve($namespace);
        $fullPath = $path . '/' . $filename;

        $description = sprintf('Creates or updates the %s %s%s%s%s',
            $settings->getType()->value,
            $object->getFullPath(),
            $settings->withDependencies() ? ' including all dependencies' : '',
            $settings->withDependencies() && $settings->withChildren() ? ' and' : '',
            $settings->withChildren() ? ' including all children' : '',
        );

        $content = $this->codeGenerator->generate('migration_template', [
            'namespace' => $namespace,
            'classname' => $classname,
            'description' => $description,
            'method_name' => $this->methodNameConverter->convert($object),
            'method_code' => $methodCode,
        ]);

        file_put_contents($fullPath, $content);

        return $fullPath;
    }
}
