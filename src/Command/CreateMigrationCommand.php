<?php

namespace PimcoreContentMigration\Command;

use function get_class;

use LogicException;
use Pimcore\Console\AbstractCommand;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Document;
use Pimcore\Model\Element\AbstractElement;
use Pimcore\Model\Listing\AbstractListing;
use PimcoreContentMigration\Factory\CodeGeneratorFactoryInterface;
use PimcoreContentMigration\Factory\SettingsFactoryInterface;
use PimcoreContentMigration\Generator\MigrationGeneratorInterface;
use PimcoreContentMigration\Generator\Settings;
use PimcoreContentMigration\Loader\ObjectLoaderInterface;
use PimcoreContentMigration\MigrationType;

use function sprintf;

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
        private readonly MigrationGeneratorInterface $migrationGenerator,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('content:migration:create')
            ->setDescription('Creates a migration file of a specific content object.')
            ->addArgument(
                'type',
                InputArgument::REQUIRED,
                sprintf(
                    'Pimcore content type (%s, %s, %s)',
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
        Concrete::setHideUnpublished(false);
        $settings = $this->settingsFactory->createSettings($input);
        $object = $this->objectLoader->loadObject($settings->getType(), $settings->getId());

        $this->generateCodeAndCreateMigrationFile($settings, $object);
        $this->generateMigrationsForChildren($object, $settings);

        return self::SUCCESS;
    }

    private function generateMigrationsForChildren(AbstractElement $object, Settings $settings): void
    {
        if (!$object instanceof DataObject\AbstractObject &&
            !$object instanceof Asset &&
            !$object instanceof Document) {
            throw new LogicException('Unsupported object type: ' . get_class($object));
        }

        if ($settings->withChildren() && $object->getChildAmount() > 0) {
            $children = $this->getChildren($object);
            /** @var AbstractElement $child */
            foreach ($children as $child) {
                $this->generateCodeAndCreateMigrationFile($settings->increaseLevel(), $child);
                $this->generateMigrationsForChildren($child, $settings->increaseLevel());
            }
        }
    }

    private function getChildren(AbstractElement $object): AbstractListing
    {
        if ($object instanceof Document) {
            return $object->getChildren(true);
        } elseif ($object instanceof Asset) {
            return $object->getChildren();
        } elseif ($object instanceof DataObject) {
            return $object->getChildren(includingUnpublished: true);
        } else {
            throw new LogicException(sprintf('Unsupported object type: %s', get_class($object)));
        }
    }

    private function generateCodeAndCreateMigrationFile(Settings $settings, AbstractElement $abstractElement): void
    {
        $code = $this->codeGeneratorFactory
            ->getCodeGenerator($settings->getType())
            ->generateCode($abstractElement, $settings);
        $migrationFilePath = $this->migrationGenerator->generateMigrationFile($abstractElement, $code, $settings);
        $this->output->writeln(sprintf('New migration file created %s for %s %s', $migrationFilePath, $abstractElement->getType(), $abstractElement->getFullPath()));
    }
}
