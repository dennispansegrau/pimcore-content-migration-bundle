<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\DataObject;

use Pimcore\Model\DataObject\Data\UrlSlug;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\ValueToStringConverterTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function sprintf;

final class UrlSlugStringifier implements ValueStringifier
{
    use ValueToStringConverterTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof UrlSlug;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var UrlSlug $value */
        $slug = $value->getSlug();
        $siteId = $value->getSiteId();

        $slugString = $this->getConverter()->valueToString($slug, $dependencyList, $parameters);
        $siteIdString = $this->getConverter()->valueToString($siteId, $dependencyList, $parameters);

        return sprintf('new \Pimcore\Model\DataObject\Data\UrlSlug(%s, %s)', $slugString, $siteIdString);
    }
}
