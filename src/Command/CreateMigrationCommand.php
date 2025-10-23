<?php

namespace DennisPansegrau\PimcoreContentMigrationBundle\Command;

use DennisPansegrau\PimcoreContentMigrationBundle\Factory\CodeGeneratorFactoryInterface;
use DennisPansegrau\PimcoreContentMigrationBundle\Loader\ObjectLoaderInterface;
use DennisPansegrau\PimcoreContentMigrationBundle\MigrationType;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class CreateMigrationCommand extends AbstractCommand
{
    private const CONTENT_TYPES = [
        MigrationType::DOCUMENT->value,
        MigrationType::ASSET->value,
        MigrationType::OBJECT->value,
    ];

    public function __construct(
        private readonly CodeGeneratorFactoryInterface $codeGeneratorFactory,
        private readonly ObjectLoaderInterface $objectLoader,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('content:migration:create')
            ->setDescription('Creates a migration file of a specific content object.')
            ->addArgument('type', InputArgument::REQUIRED, sprintf('Pimcore content type (%s, %s, %s)',
                MigrationType::DOCUMENT->value,
                MigrationType::ASSET->value,
                MigrationType::OBJECT->value
                )
            )
            ->addArgument('id', InputArgument::REQUIRED, 'Pimcore content ID')
            ->addOption(
                'namespace',
                null,
                InputOption::VALUE_REQUIRED,
                'The namespace to use for the migration (must be in the list of configured namespaces)',
            )
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $type = $this->getInputType($input);
        $id = $this->getInputId($input);
        $namespace = $input->getOption('namespace');

        $object = $this->objectLoader->loadObject($type, $id);
        $generator = $this->codeGeneratorFactory->getCodeGenerator($type);
        $generatedCode = $generator->generateCode($object);

        $command = is_string($namespace) ?
            ['bin/console', 'doctrine:migrations:generate', '--namespace=' . $namespace] :
            ['bin/console', 'doctrine:migrations:generate'];
        $process = new Process($command);
        $process->mustRun();

        if ($process->getExitCode() !== self::SUCCESS) {
            $this->output->writeln($process->getOutput());
            return self::FAILURE;
        }

        if (preg_match('/"([^"]+Version[0-9]+\.php)"/', $process->getOutput(), $matches)) {
            $migrationFilePath = $matches[1];
            $this->output->writeln(sprintf('New migration file created %s', $migrationFilePath));
        } else {
            throw new \RuntimeException('Could not find generated migration path in output.');
        }

        $content = file_get_contents($migrationFilePath);
        $content = str_replace('// this up() migration is auto-generated, please modify it to your needs', $generatedCode, $content);
        file_put_contents($migrationFilePath, $content);

        return self::SUCCESS;
    }

    private function getInputType(InputInterface $input): string
    {
        $type = $input->getArgument('type');
        if (!in_array($type, self::CONTENT_TYPES, true)) {
            throw new \InvalidArgumentException(sprintf('type "%s" is not a valid content type use "%s", "%s" or "%s".',
                $type,
                MigrationType::DOCUMENT->value,
                MigrationType::ASSET->value,
                MigrationType::OBJECT->value
            ));
        }
        return $type;
    }

    private function getInputId(InputInterface $input): int
    {
        $id = $input->getArgument('id');
        if (!is_numeric($id)) {
            throw new \InvalidArgumentException(sprintf('Argument "id" must be an integer, "%s" given.', gettype($id)));
        }
        return (int) $id;
    }
}
