<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\Trait;

use InvalidArgumentException;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function sprintf;

trait IdToDependencyStringTrait
{
    private function idToDependencyString(
        string $type,
        int $id,
        DependencyList $dependencyList,
        bool $fallbackToId = true,
        bool $forceObject = false
    ): string {
        $dependency = $dependencyList->getByTypeAndId($type, $id);

        if ($dependency === null) {
            return $fallbackToId ? (string) $id : 'null';
        }

        if ($forceObject) {
            return '$' . $dependency->getVariableName();
        }

        return '(int) $' . $dependency->getVariableName() . '->getId()';
    }

    private function idToDependencyOrFallbackToPathString(
        string $type,
        int $id,
        DependencyList $dependencyList,
        ?string $fallbackPath,
    ): string {
        $dependency = $this->idToDependencyString($type, $id, $dependencyList, false);
        if ($dependency === 'null') {
            if (empty($fallbackPath)) {
                throw new InvalidArgumentException('Fallback path must not be empty');
            }

            if ($type === 'document') {
                return sprintf('\Pimcore\Model\Document::getByPath(\'%s\')?->getId()', $fallbackPath);
            } elseif ($type === 'asset') {
                return sprintf('\Pimcore\Model\Asset::getByPath(\'%s\')?->getId()', $fallbackPath);
            } elseif ($type === 'object') {
                return sprintf('\Pimcore\Model\DataObject::getByPath(\'%s\')?->getId()', $fallbackPath);
            } else {
                throw new InvalidArgumentException('Unknown type: ' . $type . ' (allowed: document, asset, object');
            }
        }

        return $dependency;
    }
}
