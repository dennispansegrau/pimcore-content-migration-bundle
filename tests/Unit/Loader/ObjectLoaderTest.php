<?php

declare(strict_types=1);

namespace PimcoreContentMigration\Tests\Unit\Loader;

use PHPUnit\Framework\TestCase;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
use Pimcore\Model\Exception\NotFoundException;
use PimcoreContentMigration\Loader\ObjectLoader;
use PimcoreContentMigration\MigrationType;

final class ObjectLoaderTest extends TestCase
{
    protected function setUp(): void
    {
        Document::resetRegistry();
        Asset::resetRegistry();
        DataObject::resetRegistry();
    }

    public function testItLoadsDocumentAssetAndObjectByTypeAndId(): void
    {
        $document = new Document(1, '/doc');
        $asset = new Asset(2, '/asset');
        $object = new DataObject(3, '/object');

        Document::register($document);
        Asset::register($asset);
        DataObject::register($object);

        $loader = new ObjectLoader();

        self::assertSame($document, $loader->loadObject(MigrationType::DOCUMENT, 1));
        self::assertSame($asset, $loader->loadObject(MigrationType::ASSET, 2));
        self::assertSame($object, $loader->loadObject(MigrationType::OBJECT, 3));
    }

    public function testItThrowsWhenObjectCannotBeFound(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Object of type document with id 99 not found');

        (new ObjectLoader())->loadObject(MigrationType::DOCUMENT, 99);
    }
}
