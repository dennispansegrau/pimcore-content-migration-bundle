<?php

namespace PimcoreContentMigration\Command;

use PimcoreContentMigration\Factory\CodeGeneratorFactoryInterface;
use PimcoreContentMigration\Factory\SettingsFactoryInterface;
use PimcoreContentMigration\Generator\MigrationGenerator;
use PimcoreContentMigration\Loader\ObjectLoaderInterface;
use PimcoreContentMigration\MigrationType;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateMigrationCommand extends AbstractCommand
{

    public function __construct(
        private readonly SettingsFactoryInterface $settingsFactory,
        private readonly CodeGeneratorFactoryInterface $codeGeneratorFactory,
        private readonly ObjectLoaderInterface $objectLoader,
        private readonly MigrationGenerator $migrationGenerator,
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
            ->addOption(
                'with-children',
                null,
                InputOption::VALUE_NONE,
                'Include all child elements in the migration (e.g. sub-documents or child objects).',
            )
            ->addOption(
                'no-dependencies',
                null,
                InputOption::VALUE_NONE,
                'Do not include related dependencies (e.g. linked assets or objects) in the migration.',
            )
            ->addOption(
                'inline-wysiwyg',
                null,
                InputOption::VALUE_NONE,
                'Inline WYSIWYG field content directly into the migration file instead of saving it as a separate HTML file.',
            )
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        \Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);

        $settings = $this->settingsFactory->createSettings($input);
        $object = $this->objectLoader->loadObject($settings->getType(), $settings->getId());
        $generator = $this->codeGeneratorFactory->getCodeGenerator($settings->getType());
        $methodCode = $generator->generateCode($object, $settings);

        try {
            $migrationFilePath = $this->migrationGenerator->generateMigrationFile($object, $methodCode, $settings);
            $this->output->writeln(sprintf('New migration file created %s', $migrationFilePath));
        } catch (\Exception $e) {
            $this->output->writeln($e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
