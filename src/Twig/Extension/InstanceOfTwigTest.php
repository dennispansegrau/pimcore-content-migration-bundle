<?php

namespace PimcoreContentMigration\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigTest;

class InstanceOfTwigTest extends AbstractExtension
{
    public function getTests(): array
    {
        return [
            new TwigTest('instanceof', function ($object, string $class) {
                return $object instanceof $class;
            }),
        ];
    }
}
