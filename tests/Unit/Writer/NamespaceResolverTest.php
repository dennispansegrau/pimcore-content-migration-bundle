<?php

declare(strict_types=1);

namespace PimcoreContentMigration\Tests\Unit\Writer;

use Doctrine\Migrations\DependencyFactory;
use PHPUnit\Framework\TestCase;
use PimcoreContentMigration\Writer\NamespaceResolver;
use RuntimeException;

final class NamespaceResolverTest extends TestCase
{
    public function testItResolvesFirstPathWhenNamespaceIsEmpty(): void
    {
        $resolver = $this->createResolver([
            'App\Migrations' => '/tmp/migrations',
            'Other\Migrations' => '/tmp/other',
        ]);

        self::assertSame('/tmp/migrations', $resolver->resolve(null));
        self::assertSame('/tmp/migrations', $resolver->resolve(''));
    }

    public function testItResolvesExplicitNamespace(): void
    {
        $resolver = $this->createResolver([
            'App\Migrations' => '/tmp/migrations',
        ]);

        self::assertSame('/tmp/migrations', $resolver->resolve('App\Migrations'));
    }

    public function testItRejectsUnknownNamespace(): void
    {
        $resolver = $this->createResolver([
            'App\Migrations' => '/tmp/migrations',
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Migration path 'Missing\\Namespace' does not exist");

        $resolver->resolve('Missing\Namespace');
    }

    public function testItRejectsEmptyConfiguredPath(): void
    {
        $resolver = $this->createResolver([
            'App\Migrations' => '',
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No namespace defined');

        $resolver->resolve('App\Migrations');
    }

    /**
     * @param array<string, mixed> $directories
     */
    private function createResolver(array $directories): NamespaceResolver
    {
        $configuration = new class ($directories) {
            /**
             * @param array<string, mixed> $directories
             */
            public function __construct(private array $directories)
            {
            }

            /**
             * @return array<string, mixed>
             */
            public function getMigrationDirectories(): array
            {
                return $this->directories;
            }
        };

        return new NamespaceResolver(new DependencyFactory($configuration));
    }
}
