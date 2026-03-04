<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\DataObject;

use function array_key_exists;
use function is_string;

use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;
use PimcoreContentMigration\Builder\DataObject\FieldcollectionItemBuilder;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\IndentTrait;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\ValueToStringConverterTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function sprintf;

final class FieldcollectionAbstractDataStringifier implements ValueStringifier
{
    use IndentTrait;
    use ValueToStringConverterTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof AbstractData;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var AbstractData $value */
        $builderName = FieldcollectionItemBuilder::class;
        $owner = '$builder->getObject()';
        if (array_key_exists('owner', $parameters) &&
            is_string($parameters['owner'])) {
            $owner = $parameters['owner'];
        }
        return sprintf('\%s::create(\%s::class, %s)', $builderName, $value::class, $owner);
    }
}
