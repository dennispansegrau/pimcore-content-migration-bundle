<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\DataObject;

use Pimcore\Model\DataObject\Data\ElementMetadata;
use PimcoreContentMigration\Builder\DataObject\ElementMetadataBuilder;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\ValueToStringConverterTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function sprintf;

class ElementMetadataStringifier implements ValueStringifier
{
    use ValueToStringConverterTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof ElementMetadata;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var ElementMetadata $value */
        $fieldName = $value->getFieldname();
        $columns = $value->getColumns();
        $element = $value->getElement();
        $data = $value->getData();

        $builderName = ElementMetadataBuilder::class;
        $fieldNameString = $this->getConverter()->convertValueToString($fieldName, $dependencyList, $parameters);
        $columnsString = $this->getConverter()->convertValueToString($columns, $dependencyList, $parameters);
        $elementString = $this->getConverter()->convertValueToString($element, $dependencyList, $parameters);
        $dataString = $this->getConverter()->convertValueToString($data, $dependencyList, $parameters);

        return sprintf(
            '\%s::create(%s, %s, %s)->setData(%s)->getObject()',
            $builderName,
            $fieldNameString,
            $columnsString,
            $elementString,
            $dataString
        );
    }
}
