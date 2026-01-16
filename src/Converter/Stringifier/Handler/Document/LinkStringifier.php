<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\Document;

use function is_array;
use function is_int;
use function is_string;

use Pimcore\Model\Document\Editable\Link;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\IdToDependencyStringTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

class LinkStringifier implements ValueStringifier
{
    use IdToDependencyStringTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof Link;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var Link $value */
        $data = $value->getData();

        if (!is_array($data)) {
            return 'null';
        }

        $type = $data['internalType'] ?? null;
        $id = $data['internalId'] ?? null;

        if (!is_string($type) || !is_int($id)) {
            return 'null';
        }

        return $this->idToDependencyString($type, $id, $dependencyList, false);
    }
}
