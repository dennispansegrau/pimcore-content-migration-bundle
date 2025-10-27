<?php

namespace PimcoreContentMigration\Factory;

use PimcoreContentMigration\Generator\CodeGeneratorInterface;

interface CodeGeneratorFactoryInterface
{
    public function getCodeGenerator(string $type): CodeGeneratorInterface;
}
