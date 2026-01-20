<?php

namespace PimcoreContentMigration\Generator;

use DateTimeImmutable;

use function dirname;
use function file_put_contents;
use function is_dir;
use function mkdir;

use Pimcore\Model\Element\AbstractElement;
use PimcoreContentMigration\Converter\AbstractElementToMethodNameConverter;
use PimcoreContentMigration\Writer\NamespaceResolver;

use function sprintf;
use function usleep;

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

        do {
            $now = new DateTimeImmutable();
            $timestamp = $now->format('YmdHisv');
            $classname = self::PREFIX . $timestamp;

            if ($classname === self::$lastClassname) {
                usleep(1000); // 1 ms
            }
        } while ($classname === self::$lastClassname);

        self::$lastClassname = $classname;
        $filename = $classname . '.php';
        $path = $this->namespaceResolver->resolve($namespace);
        $fullPath = $path . '/' . $filename;
        $directory = dirname($fullPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

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
