<?php

declare(strict_types=1);

namespace PimcoreContentMigration\Tests\Unit\Generator\Dependency;

use LogicException;
use PHPUnit\Framework\TestCase;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
use PimcoreContentMigration\Generator\Dependency\Dependency;
use PimcoreContentMigration\Generator\Dependency\DependencyList;
use PimcoreContentMigration\Tests\Support\UnknownElement;

final class DependencyListTest extends TestCase
{
    public function testDependencyCapturesElementMetadata(): void
    {
        $document = new Document(11, '/demo');
        $asset = new Asset(12, '/asset');
        $object = new DataObject(13, '/object');

        $documentDependency = new Dependency($document, 'documentDemo', 'findOrCreateDocumentDemo', 'code');
        $assetDependency = new Dependency($asset, 'assetDemo', 'findOrCreateAssetDemo', null);
        $objectDependency = new Dependency($object, 'objectDemo', 'findOrCreateObjectDemo', 'code');

        self::assertSame($document, $documentDependency->getTarget());
        self::assertSame('document', $documentDependency->getType());
        self::assertSame(11, $documentDependency->getId());
        self::assertSame('documentDemo', $documentDependency->getVariableName());
        self::assertSame('findOrCreateDocumentDemo', $documentDependency->getMethodName());
        self::assertSame('code', $documentDependency->getCode());

        self::assertSame('asset', $assetDependency->getType());
        self::assertNull($assetDependency->getCode());
        self::assertSame('object', $objectDependency->getType());
    }

    public function testDependencyRejectsElementsWithoutId(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Target element must have an ID');

        new Dependency(new Document(null, '/demo'), 'var', 'method', null);
    }

    public function testDependencyRejectsUnknownElementTypes(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Unknown element type');

        new Dependency(new UnknownElement(99, '/custom'), 'var', 'method', null);
    }

    public function testDependencyListAvoidsDuplicatesAndSupportsLookups(): void
    {
        $document = new Document(11, '/demo');
        $asset = new Asset(12, '/asset');
        $documentDependency = new Dependency($document, 'documentDemo', 'findOrCreateDocumentDemo', 'code');
        $duplicateDocumentDependency = new Dependency(new Document(11, '/other'), 'other', 'other', null);
        $assetDependency = new Dependency($asset, 'assetDemo', 'findOrCreateAssetDemo', null);

        $dependencies = new DependencyList([$documentDependency]);
        $dependencies->add($duplicateDocumentDependency);
        $dependencies->add($assetDependency);

        self::assertCount(2, $dependencies);
        self::assertSame($documentDependency, $dependencies->getDependency($document));
        self::assertNull($dependencies->getDependency(new Document(99, '/missing')));
        self::assertSame($assetDependency, $dependencies->getByTypeAndId('asset', 12));
        self::assertNull($dependencies->getByTypeAndId('object', 99));
        self::assertSame([$documentDependency, $assetDependency], iterator_to_array($dependencies));
    }
}
