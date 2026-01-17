<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\DataObject;

use Pimcore\Model\DataObject\Data\BlockElement;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\ValueToStringConverterTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function sprintf;

final class BlockElementStringifier implements ValueStringifier
{
    use ValueToStringConverterTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof BlockElement;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var BlockElement $value */
        $name = $value->getName();
        $type = $value->getType();
        $data = $value->getData();
        $dataString = $this->getConverter()->convertValueToString($data, $dependencyList, $parameters);

        return sprintf(
            'new \Pimcore\Model\DataObject\Data\BlockElement(\'%s\', \'%s\', %s)',
            $name,
            $type,
            $dataString
        );
    }
}
