<?php

namespace PimcoreContentMigration;

use InvalidArgumentException;

use function sprintf;
use function strtolower;
use function trim;

enum MigrationType: string
{
    case DOCUMENT = 'document';
    case ASSET = 'asset';
    case OBJECT = 'object';

    public static function fromString(string $type): self
    {
        $type = strtolower(trim($type));

        return match ($type) {
            self::DOCUMENT->value => self::DOCUMENT,
            self::ASSET->value => self::ASSET,
            self::OBJECT->value => self::OBJECT,
            default => throw new InvalidArgumentException(sprintf(
                'type "%s" is not a valid content type use "%s", "%s" or "%s".',
                $type,
                MigrationType::DOCUMENT->value,
                MigrationType::ASSET->value,
                MigrationType::OBJECT->value
            ))
        };
    }
}
