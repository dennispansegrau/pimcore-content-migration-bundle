<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\DataObject\EcommerceFrameworkBundle;

use function class_exists;

use Pimcore\Bundle\EcommerceFrameworkBundle\CoreExtensions\ObjectData\IndexFieldSelection;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\ValueToStringConverterTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function sprintf;

class IndexFieldSelectionStringifier implements ValueStringifier
{
    use ValueToStringConverterTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return class_exists(IndexFieldSelection::class) &&
            $value instanceof IndexFieldSelection;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var IndexFieldSelection $value */
        $tenant = $value->getTenant();
        $field = $value->getField();
        $preSelect = $value->getPreSelect();

        $tenantString = $this->getConverter()->convertValueToString($tenant, $dependencyList, $parameters);
        $preSelectString = $this->getConverter()->convertValueToString($preSelect, $dependencyList, $parameters);

        return sprintf(
            'new \Pimcore\Bundle\EcommerceFrameworkBundle\CoreExtensions\ObjectData\IndexFieldSelection(%s, \'%s\', %s)',
            $tenantString,
            $field,
            $preSelectString
        );
    }
}
