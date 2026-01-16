<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\Document;

use InvalidArgumentException;

use function is_int;

use Pimcore\Model\Document\Editable\Video;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\IdToDependencyStringTrait;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\IndentTrait;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\ValueToStringConverterTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

class VideoStringifier implements ValueStringifier
{
    use IdToDependencyStringTrait;
    use IndentTrait;
    use ValueToStringConverterTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof Video;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var Video $value */
        if (!isset($parameters['field'])) {
            throw new InvalidArgumentException(
                'Editable type video needs field parameter with value id or poster.'
            );
        }

        $field = $parameters['field'];

        $id = match ($field) {
            'id' => $value->getId(),
            'poster' => $value->getPoster(),
            default => throw new InvalidArgumentException(
                'Editable type video needs field parameter with value id or poster.'
            ),
        };

        if (!is_int($id)) {
            return $this->getConverter()->valueToString($id, $dependencyList, $parameters);
        }

        return $this->idToDependencyString(
            'asset',
            $id,
            $dependencyList
        );
    }
}
