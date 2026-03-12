<?php

declare(strict_types=1);

namespace PimcoreContentMigration\Tests\Unit\Generator;

use Doctrine\Migrations\DependencyFactory;
use PHPUnit\Framework\TestCase;
use Pimcore\Model\Document;
use PimcoreContentMigration\Generator\CodeGenerator;
use PimcoreContentMigration\Generator\MigrationGenerator;
use PimcoreContentMigration\Generator\Settings;
use PimcoreContentMigration\MigrationType;
use PimcoreContentMigration\Writer\NamespaceResolver;
use Twig\Environment;

use function file_get_contents;
use function sys_get_temp_dir;
use function uniqid;

final class MigrationGeneratorTest extends TestCase
{
    private string $outputDirectory;

    protected function setUp(): void
    {
        $this->outputDirectory = sys_get_temp_dir() . '/pcmb-tests-' . uniqid('', true);
    }

    public function testItGeneratesMigrationFileWithExpectedContent(): void
    {
        $twig = new Environment([
            'migration.php.twig' => 'migration content',
        ]);

        $codeGenerator = new CodeGenerator($twig, [
            'migration_template' => 'migration.php.twig',
        ]);

        $resolver = new NamespaceResolver(new DependencyFactory(new class ($this->outputDirectory) {
            public function __construct(private string $directory)
            {
            }

            public function getMigrationDirectories(): array
            {
                return [
                    'App\Migrations' => $this->directory,
                ];
            }
        }));

        $generator = new MigrationGenerator(
            $codeGenerator,
            $resolver,
            new \PimcoreContentMigration\Converter\AbstractElementToMethodNameConverter()
        );

        $document = new Document(5, '/news/example');
        $settings = new Settings(MigrationType::DOCUMENT, 5, 'App\Migrations');

        $path = $generator->generateMigrationFile($document, 'method code', $settings);

        self::assertFileExists($path);
        self::assertStringStartsWith($this->outputDirectory . '/Version', $path);
        self::assertSame('migration content', file_get_contents($path));
    }

    public function testItCreatesDistinctMigrationFilesOnRepeatedCalls(): void
    {
        $twig = new Environment([
            'migration.php.twig' => 'migration content',
        ]);

        $generator = new MigrationGenerator(
            new CodeGenerator($twig, ['migration_template' => 'migration.php.twig']),
            new NamespaceResolver(new DependencyFactory(new class ($this->outputDirectory) {
                public function __construct(private string $directory)
                {
                }

                public function getMigrationDirectories(): array
                {
                    return [
                        'App\Migrations' => $this->directory,
                    ];
                }
            })),
            new \PimcoreContentMigration\Converter\AbstractElementToMethodNameConverter()
        );

        $document = new Document(5, '/news/example');
        $settings = new Settings(MigrationType::DOCUMENT, 5, 'App\Migrations');

        $first = $generator->generateMigrationFile($document, 'method code', $settings);
        $second = $generator->generateMigrationFile($document, 'method code', $settings);

        self::assertNotSame($first, $second);
        self::assertFileExists($first);
        self::assertFileExists($second);
    }
}
