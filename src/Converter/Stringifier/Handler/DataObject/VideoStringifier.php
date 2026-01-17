<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\DataObject;

use Pimcore\Model\DataObject\Data\Video;
use PimcoreContentMigration\Builder\DataObject\VideoBuilder;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\ValueToStringConverterTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function sprintf;

final readonly class VideoStringifier implements ValueStringifier
{
    use ValueToStringConverterTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof Video;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var Video $value */
        $builderName = VideoBuilder::class;
        $data = [
            'type' => $value->getType() ?? '',
            'data' => $value->getData(),
            'poster' => $value->getPoster(),
            'title' => $value->getTitle() ?? '',
            'description' => $value->getDescription() ?? '',
        ];
        $dataString = $this->getConverter()->convertValueToString($data, $dependencyList, $parameters);
        return sprintf('\%s::create()->setData(%s)->getObject()', $builderName, $dataString);
    }
}
