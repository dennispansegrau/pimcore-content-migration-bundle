<?php

declare(strict_types=1);

namespace PimcoreContentMigration\Tests\Unit\Generator;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PimcoreContentMigration\Generator\CodeGenerator;
use Twig\Environment;

final class CodeGeneratorTest extends TestCase
{
    public function testItRendersConfiguredTemplates(): void
    {
        $twig = new Environment([
            'migration.php.twig' => 'rendered template',
        ]);

        $generator = new CodeGenerator($twig, [
            'migration_template' => 'migration.php.twig',
        ]);

        self::assertSame('rendered template', $generator->generate('migration_template', ['foo' => 'bar']));
    }

    public function testItRejectsUnknownTemplateKeys(): void
    {
        $generator = new CodeGenerator(new Environment(), []);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown template key "missing".');

        $generator->generate('missing');
    }
}
