<?php

declare(strict_types=1);

namespace PimcoreContentMigration\Tests\Unit\Converter;

use LogicException;
use PHPUnit\Framework\TestCase;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
use PimcoreContentMigration\Converter\AbstractElementToMethodNameConverter;
use PimcoreContentMigration\Converter\AbstractElementToVariableNameConverter;
use PimcoreContentMigration\Tests\Support\UnknownElement;

final class AbstractElementConvertersTest extends TestCase
{
    public function testMethodNameConverterBuildsTypeSpecificMethodNames(): void
    {
        $converter = new AbstractElementToMethodNameConverter();

        self::assertSame(
            'findOrCreateDocumentNewsLandingPage',
            $converter->convert(new Document(1, '/news/landing-page'))
        );
        self::assertSame(
            'findOrCreateAssetMediaProductShot01',
            $converter->convert(new Asset(2, '/media/product_shot-01'))
        );
        self::assertSame(
            'findOrCreateObjectRoot',
            $converter->convert(new DataObject(3, '/'))
        );
    }

    public function testVariableNameConverterBuildsTypeSpecificVariableNames(): void
    {
        $converter = new AbstractElementToVariableNameConverter();

        self::assertSame(
            'documentNewsLandingPage',
            $converter->convert(new Document(1, '/news/landing-page'))
        );
        self::assertSame(
            'assetMediaProductShot01',
            $converter->convert(new Asset(2, '/media/product_shot-01'))
        );
        self::assertSame(
            'objectRoot',
            $converter->convert(new DataObject(3, '/'))
        );
    }

    public function testConvertersRejectUnknownElementTypes(): void
    {
        $element = new UnknownElement(99, '/custom');

        $methodConverter = new AbstractElementToMethodNameConverter();
        $variableConverter = new AbstractElementToVariableNameConverter();

        try {
            $methodConverter->convert($element);
            self::fail('Expected LogicException for method converter.');
        } catch (LogicException $exception) {
            self::assertStringContainsString('Unknown element type', $exception->getMessage());
        }

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Unknown element type');

        $variableConverter->convert($element);
    }
}
