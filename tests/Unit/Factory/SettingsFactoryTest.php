<?php

declare(strict_types=1);

namespace PimcoreContentMigration\Tests\Unit\Factory;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PimcoreContentMigration\Factory\SettingsFactory;
use PimcoreContentMigration\MigrationType;
use Symfony\Component\Console\Input\InputInterface;

final class SettingsFactoryTest extends TestCase
{
    public function testItBuildsSettingsFromInputAndDefaultNamespace(): void
    {
        $input = $this->createInput('document', '12', null, true, true);

        $settings = (new SettingsFactory('App\\Migrations'))->createSettings($input);

        self::assertSame(MigrationType::DOCUMENT, $settings->getType());
        self::assertSame(12, $settings->getId());
        self::assertSame('App\\Migrations', $settings->getNamespace());
        self::assertTrue($settings->withChildren());
        self::assertTrue($settings->inlineWysiwyg());
    }

    public function testItPrefersExplicitNamespace(): void
    {
        $input = $this->createInput('asset', '7', 'Custom\\Namespace', false, false);

        $settings = (new SettingsFactory('App\\Migrations'))->createSettings($input);

        self::assertSame(MigrationType::ASSET, $settings->getType());
        self::assertSame('Custom\\Namespace', $settings->getNamespace());
    }

    public function testItRejectsNonStringTypeArgument(): void
    {
        $input = $this->createInput(['document'], '1', 'App\\Migrations', false, false);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument "type" must be a string');

        (new SettingsFactory('App\\Migrations'))->createSettings($input);
    }

    public function testItRejectsNonNumericIdArgument(): void
    {
        $input = $this->createInput('document', 'abc', 'App\\Migrations', false, false);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument "id" must be an integer');

        (new SettingsFactory('App\\Migrations'))->createSettings($input);
    }

    public function testItRejectsInvalidNamespaceOptionType(): void
    {
        $input = $this->createInput('document', '1', ['bad'], false, false);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Option "namespace" must be a string');

        (new SettingsFactory('App\\Migrations'))->createSettings($input);
    }

    public function testItRequiresNamespaceWhenNoDefaultExists(): void
    {
        $input = $this->createInput('document', '1', null, false, false);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Please provide a namespace');

        (new SettingsFactory(null))->createSettings($input);
    }

    /**
     * @param mixed $type
     * @param mixed $id
     * @param mixed $namespace
     */
    private function createInput(mixed $type, mixed $id, mixed $namespace, bool $withChildren, bool $inlineWysiwyg): InputInterface
    {
        /** @var InputInterface&MockObject $input */
        $input = $this->createMock(InputInterface::class);
        $input->method('getArgument')->willReturnMap([
            ['type', $type],
            ['id', $id],
        ]);
        $input->method('getOption')->willReturnMap([
            ['namespace', $namespace],
            ['with-children', $withChildren],
            ['inline-wysiwyg', $inlineWysiwyg],
        ]);

        return $input;
    }
}

