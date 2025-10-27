<?php

namespace DennisPansegrau\PimcoreContentMigrationBundle\Generator;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Process\Process;

class MigrationGenerator
{
    public function generateMigrationFile(string $code, ?string $namespace): string
    {
        $command = is_string($namespace) ?
            ['bin/console', 'doctrine:migrations:generate', '--namespace=' . $namespace] :
            ['bin/console', 'doctrine:migrations:generate'];
        $process = new Process($command);
        $process->mustRun();

        if ($process->getExitCode() !== Command::SUCCESS) {
            throw new GenerateMigrationFileException("Command bin/console doctrine:migrations:generate failed! \n" . $process->getOutput());
        }

        if (preg_match('/"([^"]+Version[0-9]+\.php)"/', $process->getOutput(), $matches)) {
            $migrationFilePath = $matches[1];
        } else {
            throw new GenerateMigrationFileException('Could not find generated migration path in output of doctrine:migrations:generate command.');
        }

        $content = file_get_contents($migrationFilePath);
        $content = str_replace('// this up() migration is auto-generated, please modify it to your needs', $code, $content);
        file_put_contents($migrationFilePath, $content);

        return $migrationFilePath;
    }
}
