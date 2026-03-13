<?php

declare(strict_types=1);

namespace PimcoreContentMigration\Tests\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PimcoreContentMigration\MigrationType;

final class MigrationTypeTest extends TestCase
{
    public function testItParsesKnownTypesCaseInsensitively(): void
    {
        self::assertSame(MigrationType::DOCUMENT, MigrationType::fromString(' document '));
        self::assertSame(MigrationType::ASSET, MigrationType::fromString('ASSET'));
        self::assertSame(MigrationType::OBJECT, MigrationType::fromString('Object'));
    }

    public function testItRejectsUnknownTypes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('type "foo" is not a valid content type');

        MigrationType::fromString('foo');
    }
}
