<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\DataObject;

use Pimcore\Model\DataObject\Data\Link;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\IdToDependencyStringTrait;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\ValueToStringConverterTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function sprintf;

final class LinkStringifier implements ValueStringifier
{
    use IdToDependencyStringTrait;
    use ValueToStringConverterTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof Link;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var Link $value */
        $linktype = $value->getLinktype();
        $internalType = $value->getInternalType();
        $internal = $value->getInternal();

        $valuesString = $this->getConverter()->valueToString([
            'text' => $value->getText(),
            'internalType' => $internalType,
            'internal' => $internal,
            'direct' => $value->getDirect(),
            'linktype' => $linktype,
            'target' => $value->getTarget(),
            'parameters' => $value->getParameters(),
            'anchor' => $value->getAnchor(),
            'title' => $value->getTitle(),
            'accesskey' => $value->getAccesskey(),
            'rel' => $value->getRel(),
            'tabindex' => $value->getTabindex(),
            'class' => $value->getClass(),
            'attributes' => $value->getAttributes(),
        ], $dependencyList, $parameters);

        $element = '';
        if ($linktype === 'internal' && !empty($internalType) && !empty($internal)) {
            if ($dependency = $this->idToDependencyString($internalType, $internal, $dependencyList, false, true)) {
                $element = '->setElement(' . $dependency . ')';
            }
        }

        return sprintf('(new \Pimcore\Model\DataObject\Data\Link())->setValues(%s)%s', $valuesString, $element);
    }
}
