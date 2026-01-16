<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\Document;

use Pimcore\Model\Document\Editable\Snippet;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\IdToDependencyStringTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

class SnippetStringifier implements ValueStringifier
{
    use IdToDependencyStringTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof Snippet;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var Snippet $value */
        $snippet = $value->getSnippet();

        if ($snippet === null) {
            return (string) $value->getId();
        }

        return $this->idToDependencyString(
            'document',
            $value->getId(),
            $dependencyList
        );
    }
}
