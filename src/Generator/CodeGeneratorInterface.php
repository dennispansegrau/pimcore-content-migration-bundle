<?php

namespace PimcoreContentMigration\Generator;

use Pimcore\Model\Element\AbstractElement;

interface CodeGeneratorInterface
{
    /**
     * @param AbstractElement $abstractElement
     * @param Settings $settings
     * @param string[] $existingMethodNames
     */
    public function generateCode(AbstractElement $abstractElement, Settings $settings, array &$existingMethodNames = []): string;
}
