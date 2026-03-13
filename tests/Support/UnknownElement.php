<?php

declare(strict_types=1);

namespace PimcoreContentMigration\Tests\Support;

use Pimcore\Model\Element\AbstractElement;
use Pimcore\Model\Element\ElementInterface;

final class UnknownElement extends AbstractElement
{
    public function __construct(?int $id = null, string $fullPath = '/custom')
    {
        parent::__construct($id, 'unknown', $fullPath);
    }

    public function setKey(string $key)
    {
        return parent::setKey($key);
    }

    public function getKey(): string
    {
        return parent::getKey();
    }

    public function getRealFullPath(): string
    {
        return parent::getRealFullPath();
    }

    public function clearDependentCache(): void
    {
        parent::clearDependentCache();
    }

    public static function getById(int $id): ?ElementInterface
    {
        return null;
    }

    public function save(array $parameters = [])
    {
        return parent::save($parameters);
    }

    public function getRealPath(): string
    {
        return parent::getRealPath();
    }

    public function delete(): void
    {
        parent::delete();
    }

    public function setParent(mixed $parent)
    {
        return parent::setParent($parent);
    }

    public function setType(string $type)
    {
        return parent::setType($type);
    }

    public static function getTypes(): array
    {
        return [];
    }
}
