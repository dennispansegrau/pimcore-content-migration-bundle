<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\DataObject;

use function array_key_exists;
use function array_keys;
use function implode;
use function is_string;

use Pimcore\Model\DataObject\Objectbrick\Data\AbstractData;
use PimcoreContentMigration\Builder\DataObject\ObjectbrickItemBuilder;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\IndentTrait;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\ValueToStringConverterTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function sprintf;
use function str_repeat;

final class ObjectbrickAbstractDataStringifier implements ValueStringifier
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
        $fields = array_keys($value->getDefinition()->getFieldDefinitions());
        $values = [];
        foreach ($fields as $field) {
            $values[$field] = $value->get($field);
        }

        $indent = $this->getIndent($parameters);
        $setter = [];
        foreach ($values as $field => $fieldValue) {
            $setter[] = sprintf('%s->set(\'%s\', %s)', str_repeat(' ', $indent), $field, $this->getConverter()->convertValueToString($fieldValue, $dependencyList, $parameters));
        }

        $setterString = '';
        if (!empty($setter)) {
            $setterString = "\n" . implode("\n", $setter) . "\n";
        }

        $builderName = ObjectbrickItemBuilder::class;
        $owner = '$builder->getObject()';
        if (array_key_exists('owner', $parameters) &&
            is_string($parameters['owner'])) {
            $owner = $parameters['owner'];
        }
        return sprintf('\%s::create(\%s::class, %s)%s->getObject()', $builderName, $value::class, $owner, $setterString);
    }
}
