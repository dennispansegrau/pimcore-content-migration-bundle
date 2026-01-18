<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\DataObject;

use function array_key_exists;
use function is_string;

use Pimcore\Model\DataObject\Classificationstore;
use PimcoreContentMigration\Builder\DataObject\ClassificationstoreBuilder;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\ValueToStringConverterTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function sprintf;

class ClassificationstoreStringifier implements ValueStringifier
{
    use ValueToStringConverterTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof Classificationstore;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var Classificationstore $value */
        $builderName = ClassificationstoreBuilder::class;
        $owner = '$builder->getObject()';
        if (array_key_exists('owner', $parameters) &&
            is_string($parameters['owner'])) {
            $owner = $parameters['owner'];
        }
        $items = $this->getConverter()->convertValueToString($value->getItems(), $dependencyList, $parameters);
        return sprintf('\%s::create(\'%s\', %s, %s)->getObject()', $builderName, $value->getFieldname(), $owner, $items);
    }
}
