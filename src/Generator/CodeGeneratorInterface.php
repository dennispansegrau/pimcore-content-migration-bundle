<?php

namespace PimcoreContentMigration\Generator;

/**
 * @template T
 */
interface CodeGeneratorInterface
{
    /**
     * @param T $object
     * @param Settings $settings
     */
    public function generateCode(object $object, Settings $settings, array &$existingMethodNames = []): string;
}
