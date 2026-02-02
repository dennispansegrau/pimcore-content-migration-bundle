<?php

namespace PimcoreContentMigration\Builder\Asset;

use function basename;
use function dirname;

use Exception;

use function file_get_contents;

use LogicException;
use Pimcore\Model\Asset;
use Pimcore\Model\Element\DuplicateFullPathException;
use PimcoreContentMigration\Builder\AbstractElementBuilder;

use function random_int;

use RuntimeException;

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
    public static function findOrCreate(string $path, ?string $dataPath = null): static
    {
        $builder = new static();
        /** @var class-string<Asset> $assetClass */
        $assetClass = static::getAssetClass();

        $builder->asset = Asset::getByPath($path);
        if (!$builder->asset instanceof Asset) {
            $parentPath = dirname($path);
            $filename = basename($path);
            $builder->asset = $builder->createAsset($assetClass, $parentPath, $filename, $dataPath);
        }

        // the object already exists but is not of the correct type
        if (!$builder->asset instanceof $assetClass) {
            $parentPath = dirname($path);
            $tempFilename = 'temp_' . basename($path) . '_' . random_int(1000, 9999);
            try {
                $tempObject = $builder->createAsset($assetClass, $parentPath, $tempFilename, $dataPath);
                $builder->replaceAsset($builder->asset, $tempObject);
            } catch (Exception $exception) {
                $tempObject = Asset::getByPath($parentPath . '/' . $tempFilename);
                $tempObject?->delete();
            }
        }

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

    /**
     * @throws Exception
     */
    private function getParentByPath(string $parentPath): Asset
    {
        $parent = null;
        if (Asset\Service::pathExists($parentPath)) {
            $parent = Asset::getByPath($parentPath);
        }

        if ($parent === null) {
            $parent = Asset\Service::createFolderByPath($parentPath);
        }

        if (!$parent instanceof Asset) {
            throw new Exception("Parent asset not found for path: $parentPath");
        }

        return $parent;
    }

    /**
     * @throws Exception
     */
    private function createAsset(string $assetClass, string $parentPath, string $filename, ?string $dataPath): Asset
    {
        $asset = new $assetClass();
        if (!$asset instanceof Asset) {
            throw new Exception("Class $assetClass is not an Asset");
        }
        $asset->setFilename($filename);
        $parent = $this->getParentByPath($parentPath);
        $asset->setParent($parent);

        if ($dataPath !== null) {
            $data = file_get_contents($dataPath);
            if ($data === false) {
                throw new RuntimeException("Could not read file: $dataPath");
            }
            $asset->setData($data);
        }

        $asset->save();
        return $asset;
    }

    /**
     * @throws DuplicateFullPathException
     * @throws Exception
     */
    private function replaceAsset(Asset $oldAsset, Asset $newAsset): void
    {
        $children = $oldAsset->getChildren();
        foreach ($children as $child) {
            if (!$child instanceof Asset) {
                continue;
            }
            $child->setParent($newAsset);
            $child->save();
        }

        $oldKey = $oldAsset->getKey();
        $oldAsset->delete();

        if ($oldKey === null) {
            throw new LogicException('Old asset has no key');
        }
        $newAsset->setKey($oldKey);
        $newAsset->save();

        $this->asset = $newAsset;
    }
}
