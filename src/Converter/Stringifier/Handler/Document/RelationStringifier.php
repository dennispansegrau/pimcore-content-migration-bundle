<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\Document;

use function is_array;
use function is_int;
use function is_string;

use Pimcore\Model\Document\Editable\Relation;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\IdToDependencyStringTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

class RelationStringifier implements ValueStringifier
{
    use IdToDependencyStringTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof Relation;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var Relation $value */
        $data = $value->getData();

        if (!is_array($data) || !isset($data['type'], $data['id'])) {
            return 'null';
        }

        if (!is_string($data['type']) || !is_int($data['id'])) {
            return 'null';
        }

        return $this->idToDependencyString($data['type'], $data['id'], $dependencyList);
    }
}
