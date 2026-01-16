<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\DataObject;

use function array_key_exists;
use function is_array;
use function is_string;

use Pimcore\Model\DataObject\Objectbrick;
use PimcoreContentMigration\Builder\DataObject\ObjectbrickBuilder;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\ValueToStringConverterTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;
use RuntimeException;

use function sprintf;

final class ObjectbrickStringifier implements ValueStringifier
{
    use ValueToStringConverterTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof Objectbrick;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var Objectbrick $value */
        $builderName = ObjectbrickBuilder::class;
        $owner = '$builder->getObject()';
        if (array_key_exists('owner', $parameters) &&
            is_string($parameters['owner'])) {
            $owner = $parameters['owner'];
        }
        $setterString = '';
        $items = $value->getItems();
        if (!empty($items)) {
            $setterString = "->setItems([\n";
            foreach ($items as $item) {
                if (!is_array($item)) {
                    throw new RuntimeException('Invalid objectbrick item.');
                }
                $setterString .= $this->getConverter()->valueToString($item, $dependencyList, $parameters) . ",\n";
            }
            $setterString .= "\n])";
        }
        return sprintf('\%s::create(\'%s\', %s)%s->getObject()', $builderName, $value->getFieldname(), $owner, $setterString);
    }
}
