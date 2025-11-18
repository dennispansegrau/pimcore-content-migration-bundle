<?php

namespace PimcoreContentMigration\Tests\Unit\Writer;

use PHPUnit\Framework\TestCase;
use PimcoreContentMigration\Writer\RelativePath;

class RelativePathTest extends TestCase
{
    public function testCanCreateRelativePath(): void
    {
        $name = 'example.txt';
        $path = 'some/folder/example.txt';

        $relativePath = new RelativePath($name, $path);

        // Prüft, dass die Getter die korrekten Werte zurückgeben
        $this->assertSame($name, $relativePath->getName());
        $this->assertSame($path, $relativePath->getRelativePath());
    }
}
