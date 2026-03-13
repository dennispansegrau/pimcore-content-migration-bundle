<?php

declare(strict_types=1);

namespace PimcoreContentMigration\Tests\Unit\Writer;

use PHPUnit\Framework\TestCase;
use Pimcore\Model\Document\Editable;
use PimcoreContentMigration\Writer\RelativePath;

final class RelativePathTest extends TestCase
{
    public function testItStoresRelativePathMetadataAndEditable(): void
    {
        $relativePath = new RelativePath('wysiwyg', 'content/example.html');

        self::assertSame('wysiwyg', $relativePath->getName());
        self::assertSame('content/example.html', $relativePath->getRelativePath());
        self::assertNull($relativePath->getEditable());

        $editable = new Editable();
        $relativePath->setEditable($editable);

        self::assertSame($editable, $relativePath->getEditable());
    }
}
