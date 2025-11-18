<?php

namespace PimcoreContentMigration\Tests\Unit\Writer;

use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\DependencyFactory;
use PHPUnit\Framework\TestCase;
use PimcoreContentMigration\Writer\NamespaceResolver;
use RuntimeException;

class NamespaceResolverTest extends TestCase
{
    public function testResolveReturnsFirstDirectoryIfNamespaceIsNull(): void
    {
        $migrationDirs = [
            'default' => '/path/to/migrations',
            'other' => '/path/to/other',
        ];

        $config = $this->createMock(Configuration::class);
        $config->method('getMigrationDirectories')->willReturn($migrationDirs);

        $factory = $this->createMock(DependencyFactory::class);
        $factory->method('getConfiguration')->willReturn($config);

        $resolver = new NamespaceResolver($factory);

        $result = $resolver->resolve(null);

        $this->assertSame('/path/to/migrations', $result);
    }

    public function testResolveReturnsDirectoryForGivenNamespace(): void
    {
        $migrationDirs = [
            'default' => '/path/to/migrations',
            'custom' => '/custom/path',
        ];

        $config = $this->createMock(Configuration::class);
        $config->method('getMigrationDirectories')->willReturn($migrationDirs);

        $factory = $this->createMock(DependencyFactory::class);
        $factory->method('getConfiguration')->willReturn($config);

        $resolver = new NamespaceResolver($factory);

        $result = $resolver->resolve('custom');

        $this->assertSame('/custom/path', $result);
    }

    public function testResolveThrowsExceptionIfNamespaceDoesNotExist(): void
    {
        $migrationDirs = [
            'default' => '/path/to/migrations',
        ];

        $config = $this->createMock(Configuration::class);
        $config->method('getMigrationDirectories')->willReturn($migrationDirs);

        $factory = $this->createMock(DependencyFactory::class);
        $factory->method('getConfiguration')->willReturn($config);

        $resolver = new NamespaceResolver($factory);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Migration path 'unknown' does not exist in configuration.");

        $resolver->resolve('unknown');
    }

    public function testResolveThrowsExceptionIfNoDirectoriesDefined(): void
    {
        $config = $this->createMock(Configuration::class);
        $config->method('getMigrationDirectories')->willReturn([]);

        $factory = $this->createMock(DependencyFactory::class);
        $factory->method('getConfiguration')->willReturn($config);

        $resolver = new NamespaceResolver($factory);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No namespace defined');

        $resolver->resolve(null);
    }
}
