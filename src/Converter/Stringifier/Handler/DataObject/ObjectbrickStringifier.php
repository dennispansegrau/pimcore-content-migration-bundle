<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\DataObject;

use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\IndentTrait;
use function array_key_exists;
use function is_array;
use function is_string;

use Pimcore\Model\DataObject\Objectbrick;
use PimcoreContentMigration\Builder\DataObject\ObjectbrickBuilder;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\ValueToStringConverterTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function sprintf;

final class ObjectbrickStringifier implements ValueStringifier
{
    use ValueToStringConverterTrait;
    use IndentTrait;

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
        $indent = $this->getAndIncreaseIndent($parameters);
        if (!empty($items)) {
            $setterString = "->setItems([\n";
            foreach ($items as $item) {
                $setterString .= sprintf("%s%s,\n",
                    str_repeat(' ', $indent + 4),
                    $this->getConverter()->convertValueToString($item, $dependencyList, $parameters)
                );
            }
            $setterString .= str_repeat(' ', $indent) . "])";
        }
        return sprintf('\%s::create(\'%s\', %s)%s->getObject()', $builderName, $value->getFieldname(), $owner, $setterString);
    }
}
