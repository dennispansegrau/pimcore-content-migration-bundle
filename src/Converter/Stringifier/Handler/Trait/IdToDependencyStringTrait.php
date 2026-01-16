<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\Trait;

use PimcoreContentMigration\Generator\Dependency\DependencyList;

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
}
