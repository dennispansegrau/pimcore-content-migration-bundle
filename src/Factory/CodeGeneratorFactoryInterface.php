<?php

namespace DennisPansegrau\PimcoreContentMigrationBundle\Factory;

use DennisPansegrau\PimcoreContentMigrationBundle\Generator\CodeGeneratorInterface;

interface CodeGeneratorFactoryInterface
{
    public function getCodeGenerator(string $type): CodeGeneratorInterface;
}
