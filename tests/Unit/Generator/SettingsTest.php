<?php

declare(strict_types=1);

namespace PimcoreContentMigration\Tests\Unit\Generator;

use PHPUnit\Framework\TestCase;
use PimcoreContentMigration\Generator\Settings;
use PimcoreContentMigration\MigrationType;

final class SettingsTest extends TestCase
{
    public function testItExposesConstructorValues(): void
    {
        $settings = new Settings(MigrationType::DOCUMENT, 42, 'App\Migrations', true, false, true);

        self::assertSame(MigrationType::DOCUMENT, $settings->getType());
        self::assertSame(42, $settings->getId());
        self::assertSame('App\Migrations', $settings->getNamespace());
        self::assertTrue($settings->inlineWysiwyg());
        self::assertFalse($settings->withDependencies());
        self::assertTrue($settings->withChildren());
        self::assertTrue($settings->isRootLevel());
    }

    public function testForDependenciesDisablesDependenciesAndChildrenAndIncreasesLevel(): void
    {
        $settings = new Settings(MigrationType::ASSET, 5, 'App\Migrations', true, true, true);
        $dependencySettings = $settings->forDependencies();

        self::assertSame(MigrationType::ASSET, $dependencySettings->getType());
        self::assertSame(5, $dependencySettings->getId());
        self::assertSame('App\Migrations', $dependencySettings->getNamespace());
        self::assertTrue($dependencySettings->inlineWysiwyg());
        self::assertFalse($dependencySettings->withDependencies());
        self::assertFalse($dependencySettings->withChildren());
        self::assertFalse($dependencySettings->isRootLevel());
    }

    public function testIncreaseLevelKeepsFlagsAndLeavesRootLevel(): void
    {
        $settings = new Settings(MigrationType::OBJECT, 9, 'App\Migrations', false, true, true);
        $nextLevel = $settings->increaseLevel();

        self::assertSame(MigrationType::OBJECT, $nextLevel->getType());
        self::assertSame(9, $nextLevel->getId());
        self::assertSame('App\Migrations', $nextLevel->getNamespace());
        self::assertFalse($nextLevel->inlineWysiwyg());
        self::assertTrue($nextLevel->withDependencies());
        self::assertTrue($nextLevel->withChildren());
        self::assertFalse($nextLevel->isRootLevel());
    }
}
