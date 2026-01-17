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

        $brickGetters = $value->getBrickGetters();
        $items = [];
        /** @var string $getter */
        foreach ($brickGetters as $getter) {
            if (!method_exists($value, $getter)) {
                throw new \RuntimeException(sprintf('Method %s::%s does not exist.', get_class($value), $getter));
            }
            $property = substr($getter, 3);
            $items[$property] = $value->$getter();
        }
        $itemString = $this->getConverter()->convertValueToString($items, $dependencyList, $parameters);
        return sprintf('\%s::create(\'%s\', %s, %s)->getObject()', $builderName, $value->getFieldname(), $owner, $itemString);
    }
}
