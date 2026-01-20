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
        $elementId = $value->getId();

        if ($elementId === null) {
            return 'null';
        }

        return $this->idToDependencyString('asset', $elementId, $dependencyList);
    }
}
