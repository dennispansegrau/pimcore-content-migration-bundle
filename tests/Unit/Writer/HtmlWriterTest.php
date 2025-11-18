<?php

namespace PimcoreContentMigration\Tests\Unit\Writer;

use function array_map;
use function glob;
use function is_dir;
use function mkdir;

use PHPUnit\Framework\TestCase;
use Pimcore\Model\Element\AbstractElement;
use PimcoreContentMigration\Writer\HtmlWriter;
use PimcoreContentMigration\Writer\NamespaceResolver;
use PimcoreContentMigration\Writer\RelativePath;

use function rmdir;

use RuntimeException;

use function sys_get_temp_dir;

class HtmlWriterTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/html_writer_test';
        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0777, true);
        }
    }

    protected function tearDown(): void
    {
        // Einfacher Cleanup, rekursives Löschen kann erweitert werden
        array_map('unlink', glob($this->tempDir . '/*.*'));
        @rmdir($this->tempDir);
    }

    public function testWriteCreatesFileAndReturnsRelativePath(): void
    {
        $fileName = 'example';
        $fileData = '<p>Hello World</p>';
        $migrationNamespace = 'MyNamespace';

        // Mock AbstractElement
        $object = $this->createMock(AbstractElement::class);
        $object->method('getFullPath')->willReturn('/some/path');

        // Mock NamespaceResolver
        $namespaceResolver = $this->createMock(NamespaceResolver::class);
        $namespaceResolver->method('resolve')->with($migrationNamespace)->willReturn($this->tempDir);

        $writer = new HtmlWriter($namespaceResolver);
        $relativePath = $writer->write($object, $migrationNamespace, $fileName, $fileData);

        // Prüfen, dass RelativePath korrekt ist
        $this->assertInstanceOf(RelativePath::class, $relativePath);
        $this->assertSame($fileName, $relativePath->getName());
        $this->assertStringEndsWith($fileName . '.wysiwyg.html', $relativePath->getRelativePath());

        // Prüfen, dass die Datei existiert und Inhalt korrekt ist
        $absolutePath = $this->tempDir . $relativePath->getRelativePath();
        $this->assertFileExists($absolutePath);
        $this->assertStringEqualsFile($absolutePath, $fileData);
    }

    public function testWriteThrowsRuntimeExceptionOnFilePutContentsFailure(): void
    {
        $this->expectException(RuntimeException::class);

        $fileName = 'example';
        $fileData = '<p>Hello</p>';
        $migrationNamespace = 'MyNamespace';

        $namespaceResolver = $this->createMock(NamespaceResolver::class);
        // Rückgabe eines schreibgeschützten Pfads simulieren
        $namespaceResolver->method('resolve')->with($migrationNamespace)->willReturn('/dev/null');

        // Mock AbstractElement
        $object = $this->createMock(AbstractElement::class);
        $object->method('getFullPath')->willReturn('/some/path');

        $writer = new HtmlWriter($namespaceResolver);

        // Auslösen der Exception
        $writer->write($object, $migrationNamespace, $fileName, $fileData);
    }
}
