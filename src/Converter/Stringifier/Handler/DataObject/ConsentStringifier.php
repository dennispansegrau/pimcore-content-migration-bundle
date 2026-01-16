<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\DataObject;

use Pimcore\Model\DataObject\Data\Consent;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function sprintf;

final readonly class ConsentStringifier implements ValueStringifier
{
    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof Consent;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var Consent $value */
        $noteId = $value->getNoteId();
        if ($noteId === null) {
            $noteId = 'null';
        }
        return sprintf(
            'new \Pimcore\Model\DataObject\Data\Consent(%s, %s)',
            $value->getConsent() ? 'true' : 'false',
            (string) $noteId
        );
    }
}
