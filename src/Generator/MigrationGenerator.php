<?php

namespace PimcoreContentMigration\Generator;

use PimcoreContentMigration\Writer\NamespaceResolver;

readonly class MigrationGenerator
{
    private const PREFIX = 'Version';

    public function __construct(
        private CodeGenerator $codeGenerator,
        private NamespaceResolver $namespaceResolver,
    ) {
    }

    public function generateMigrationFile(string $methodName, string $methodCode, ?string $namespace, ?string $description = ''): string
    {
        $classname = self::PREFIX . date('YmdHis');
        $filename = $classname . '.php';
        $path = $this->namespaceResolver->resolve($namespace);
        $fullPath = $path . '/' . $filename;

        $content = $this->codeGenerator->generate('migration_template', [
            'namespace' => $namespace,
            'classname' => $classname,
            'description' => $description,
            'method_name' => $methodName,
            'method_code' => $methodCode,
        ]);

        file_put_contents($fullPath, $content);

        return $fullPath;
    }
}
