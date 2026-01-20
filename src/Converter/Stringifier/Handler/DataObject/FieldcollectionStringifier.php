<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\DataObject;

use Pimcore\Model\DataObject\Fieldcollection;
use PimcoreContentMigration\Builder\DataObject\FieldcollectionBuilder;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\ValueToStringConverterTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function sprintf;

final class FieldcollectionStringifier implements ValueStringifier
{
    use ValueToStringConverterTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof Fieldcollection;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var Fieldcollection<\Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData> $value */
        $builderName = FieldcollectionBuilder::class;
        $fields = $this->getConverter()->convertValueToString($value->getItems(), $dependencyList, $parameters);
        return sprintf('\%s::create(\'%s\', %s)->getObject()', $builderName, $value->getFieldname(), $fields);
    }
}
