<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\Document;

use function is_array;
use function is_int;
use function is_string;

use LogicException;
use Pimcore\Model\Document\Editable\Renderlet;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\IdToDependencyStringTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

class RenderletStringifier implements ValueStringifier
{
    use IdToDependencyStringTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof Renderlet;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var Renderlet $value */
        $data = $value->getData();

        if (!is_array($data) || !isset($data['type'], $data['id'])) {
            throw new LogicException('Invalid data.');
        }

        if (!is_string($data['type']) || !is_int($data['id'])) {
            throw new LogicException('Invalid data.');
        }

        return $this->idToDependencyString($data['type'], $data['id'], $dependencyList);
    }
}
