<?php

namespace PimcoreContentMigration\Generator;

/**
 * @template T
 */
interface CodeGeneratorInterface
{
    /**
     * @param T $object
     */
    public function generateCode(object $object): string;
}
