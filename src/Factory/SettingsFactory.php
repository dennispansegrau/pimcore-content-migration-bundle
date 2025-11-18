<?php

namespace PimcoreContentMigration\Factory;

use function gettype;

use InvalidArgumentException;

use function is_numeric;
use function is_string;

use PimcoreContentMigration\Generator\Settings;
use PimcoreContentMigration\MigrationType;

use function sprintf;

use Symfony\Component\Console\Input\InputInterface;

final class SettingsFactory implements SettingsFactoryInterface
{
    public function createSettings(InputInterface $input): Settings
    {
        $type = $this->getInputType($input);
        $id = $this->getInputId($input);
        $namespace = $input->getOption('namespace');
        $withChildren = (bool) $input->getOption('with-children');
        $noDependencies = (bool) $input->getOption('no-dependencies');
        $inlineWysiwyg = (bool) $input->getOption('inline-wysiwyg');

        if (!is_string($namespace) && $namespace !== null) {
            throw new InvalidArgumentException(sprintf('Option "namespace" must be a string, "%s" given.', gettype($namespace)));
        }

        return new Settings($type, $id, $namespace, $inlineWysiwyg, !$noDependencies, $withChildren);
    }

    private function getInputType(InputInterface $input): MigrationType
    {
        $type = $input->getArgument('type');
        if (!is_string($type)) {
            throw new InvalidArgumentException(sprintf('Argument "type" must be a string, "%s" given.', gettype($type)));
        }
        return MigrationType::fromString($type);
    }

    private function getInputId(InputInterface $input): int
    {
        $id = $input->getArgument('id');
        if (!is_numeric($id)) {
            throw new InvalidArgumentException(sprintf('Argument "id" must be an integer, "%s" given.', gettype($id)));
        }
        return (int) $id;
    }
}
