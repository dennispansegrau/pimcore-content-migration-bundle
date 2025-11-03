<?php

namespace PimcoreContentMigration\Factory;

use PimcoreContentMigration\Generator\Settings;
use PimcoreContentMigration\MigrationType;
use Symfony\Component\Console\Input\InputInterface;

final class SettingsFactory implements SettingsFactoryInterface
{
    public function createSettings(InputInterface $input): Settings
    {
        $type = $this->getInputType($input);
        $id = $this->getInputId($input);
        $namespace = $input->getOption('namespace');
        $withChildren = $input->getOption('with-children');
        $noDependencies = $input->getOption('no-dependencies');
        $inlineWysiwyg = $input->getOption('inline-wysiwyg');

        return new Settings($type, $id, $namespace, $inlineWysiwyg, !$noDependencies, $withChildren);
    }

    private function getInputType(InputInterface $input): MigrationType
    {
        $type = (string) $input->getArgument('type');
        return MigrationType::fromString($type);
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
