<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\DataObject;

use function array_key_exists;
use function is_string;

use Pimcore\Model\DataObject\Localizedfield;
use PimcoreContentMigration\Builder\DataObject\LocalizedfieldBuilder;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\ValueToStringConverterTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function sprintf;

final class LocalizedfieldStringifier implements ValueStringifier
{
    use ValueToStringConverterTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof Localizedfield;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var Localizedfield $value */
        $builderName = LocalizedfieldBuilder::class;
        $owner = '$builder->getObject()';
        if (array_key_exists('owner', $parameters) &&
            is_string($parameters['owner'])) {
            $owner = $parameters['owner'];
        }
        $values = $this->getConverter()->valueToString($value->getItems(), $dependencyList, $parameters);
        return sprintf('\%s::create(%s)->setLocalizedValues(%s)->getObject()', $builderName, $owner, $values);
    }
}
