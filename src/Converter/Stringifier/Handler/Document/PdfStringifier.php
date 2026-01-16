<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\Document;

use Pimcore\Model\Document\Editable\Pdf;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\IdToDependencyStringTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

class PdfStringifier implements ValueStringifier
{
    use IdToDependencyStringTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof Pdf;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var Pdf $value */
        $asset = $value->getElement();

        if ($asset === null) {
            return (string) $value->getId();
        }

        return $this->idToDependencyString('asset', $asset->getId(), $dependencyList);
    }
}
