<?php

namespace PimcoreContentMigration\Factory;

use PimcoreContentMigration\Generator\Settings;
use Symfony\Component\Console\Input\InputInterface;

interface SettingsFactoryInterface
{
    public function createSettings(InputInterface $input): Settings;
}
