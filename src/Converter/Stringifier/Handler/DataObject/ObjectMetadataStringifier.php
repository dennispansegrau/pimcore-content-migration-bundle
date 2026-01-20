<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\DataObject;

use Pimcore\Model\DataObject\Data\ObjectMetadata;
use PimcoreContentMigration\Builder\DataObject\ObjectMetadataBuilder;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\ValueToStringConverterTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function sprintf;

class ObjectMetadataStringifier implements ValueStringifier
{
    use ValueToStringConverterTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof ObjectMetadata;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var ObjectMetadata $value */
        $fieldName = $value->getFieldname();
        $columns = $value->getColumns();
        $object = $value->getObject();
        $data = $value->getData();

        $builderName = ObjectMetadataBuilder::class;
        $fieldNameString = $this->getConverter()->convertValueToString($fieldName, $dependencyList, $parameters);
        $columnsString = $this->getConverter()->convertValueToString($columns, $dependencyList, $parameters);
        $objectString = $this->getConverter()->convertValueToString($object, $dependencyList, $parameters);
        $dataString = $this->getConverter()->convertValueToString($data, $dependencyList, $parameters);

        return sprintf(
            '\%s::create(%s, %s, %s)->setData(%s)->getObject()',
            $builderName,
            $fieldNameString,
            $columnsString,
            $objectString,
            $dataString
        );
    }
}
