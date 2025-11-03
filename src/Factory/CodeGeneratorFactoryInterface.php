<?php

namespace PimcoreContentMigration\Factory;

use PimcoreContentMigration\Generator\CodeGeneratorInterface;
use PimcoreContentMigration\MigrationType;

interface CodeGeneratorFactoryInterface
{
    public function getCodeGenerator(MigrationType $type): CodeGeneratorInterface;
}
