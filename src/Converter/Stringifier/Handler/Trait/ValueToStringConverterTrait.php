<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\Trait;

use PimcoreContentMigration\Converter\Stringifier\ValueToStringConverter;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;

trait ValueToStringConverterTrait
{
    public function __construct(
        #[AutowireLocator('pcmb.converter')]
        private readonly ContainerInterface $locator,
    ) {
    }

    public function getConverter(): ValueToStringConverter
    {
        /** @var ValueToStringConverter $converter */
        $converter = $this->locator->get(ValueToStringConverter::class);
        return $converter;
    }
}
