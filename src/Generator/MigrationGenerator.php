<?php

namespace PimcoreContentMigration\Generator;

use function date;
use function file_put_contents;

use Pimcore\Model\Element\AbstractElement;
use PimcoreContentMigration\Converter\AbstractElementToMethodNameConverter;
use PimcoreContentMigration\Writer\NamespaceResolver;

use function sleep;
use function sprintf;

class MigrationGenerator implements MigrationGeneratorInterface
{
    private const PREFIX = 'Version';

    private static ?string $lastClassname = null;   // ensures a unique class name

    public function __construct(
        private readonly CodeGenerator $codeGenerator,
        private readonly NamespaceResolver $namespaceResolver,
        private readonly AbstractElementToMethodNameConverter $methodNameConverter,
    ) {
    }

    public function generateMigrationFile(AbstractElement $object, string $methodCode, Settings $settings): string
    {
        $namespace = $settings->getNamespace();
        $classname = self::PREFIX . date('YmdHis');
        if ($classname === self::$lastClassname) {
            sleep(1);
            $classname = self::PREFIX . date('YmdHis');
        };
        self::$lastClassname = $classname;
        $filename = $classname . '.php';
        $path = $this->namespaceResolver->resolve($namespace);
        $fullPath = $path . '/' . $filename;

        $description = sprintf(
            'Creates or updates the %s %s%s',
            $settings->getType()->value,
            $object->getFullPath(),
            $settings->withDependencies() ? ' including all dependencies' : '',
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
