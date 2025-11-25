<?php

namespace PimcoreContentMigration\Builder\Asset;

use function basename;
use function dirname;

use Exception;

use function file_get_contents;
use function json_decode;

use LogicException;
use Pimcore\Model\Asset;
use Pimcore\Model\Element\DuplicateFullPathException;
use PimcoreContentMigration\Builder\AbstractElementBuilder;
use RuntimeException;

use function str_replace;

class AssetBuilder extends AbstractElementBuilder
{
    protected ?Asset $asset = null;

    protected static function getAssetClass(): string
    {
        return Asset::class;
    }

    /**
     * @throws Exception
     */
    public static function createOrUpdate(string $path, ?string $dataPath = null): static
    {
        $builder = new static();
        /** @var class-string<Asset> $assetClass */
        $assetClass = static::getAssetClass();

        $builder->asset = $assetClass::getByPath($path);
        if (!$builder->asset instanceof $assetClass) {
            $builder->asset = new $assetClass();
            $filename = basename($path);
            $parentPath = dirname($path);
            $builder->asset->setFilename($filename);
            $parent = Asset::getByPath($parentPath);
            if (!$parent instanceof Asset) {
                throw new Exception("Parent asset not found for path: $parentPath");
            }
            $builder->asset->setParentId($parent->getId());
        }
        if ($dataPath !== null) {
            $data = file_get_contents($dataPath);
            if ($data === false) {
                throw new RuntimeException("Could not read file: $path");
            }
            $builder->asset->setData($data);
        }
        $builder->asset->save(); // must be already saved for some actions

        return $builder;
    }

    public function getObject(): Asset
    {
        if (null === $this->asset) {
            throw new LogicException('Asset object has not been set');
        }
        return $this->asset;
    }

    public function setDataModificationDate(?int $dataModificationDate): static
    {
        $this->getObject()->setDataModificationDate($dataModificationDate);
        return $this;
    }

    /**
     * @param array<string, string> $parameters
     * @return $this
     * @throws DuplicateFullPathException
     */
    public function save(array $parameters = []): static
    {
        $this->getObject()->save($parameters);
        return $this;
    }

    public function setFilename(string $filename): static
    {
        $this->getObject()->setFilename($filename);
        return $this;
    }

    public function setDataChanged(bool $changed = true): static
    {
        $this->getObject()->setDataChanged($changed);
        return $this;
    }

    public function setVersions(array $versions): static
    {
        $this->getObject()->setVersions($versions);
        return $this;
    }

    public function setCustomSetting(string $key, mixed $value): static
    {
        $this->getObject()->setCustomSetting($key, $value);
        return $this;
    }

    public function setCustomSettings(mixed $customSettings): static
    {
        $this->getObject()->setCustomSettings($customSettings);
        return $this;
    }

    public function setMimeType(string $mimetype): static
    {
        $this->getObject()->setMimeType($mimetype);
        return $this;
    }

    public function setMetadataRaw(array $metadata): static
    {
        $this->getObject()->setMetadataRaw($metadata);
        return $this;
    }

    public function setMetadata(array $metadata): static
    {
        $this->getObject()->setMetadata($metadata);
        return $this;
    }

    public function setHasMetaData(bool $hasMetaData): static
    {
        $this->getObject()->setHasMetaData($hasMetaData);
        return $this;
    }

    public function addMetadata(string $name, string $type, mixed $data = null, ?string $language = null): static
    {
        $this->getObject()->addMetadata($name, $type, $data, $language);
        return $this;
    }

    public function setType(string $type): static
    {
        $this->getObject()->setType($type);
        return $this;
    }
}
