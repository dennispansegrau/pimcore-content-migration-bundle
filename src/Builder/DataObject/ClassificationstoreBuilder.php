<?php

namespace PimcoreContentMigration\Builder\DataObject;

use Exception;
use LogicException;
use Pimcore\Model\DataObject\Classificationstore;
use Pimcore\Model\DataObject\Classificationstore\CollectionConfig;
use Pimcore\Model\DataObject\Classificationstore\CollectionGroupRelation;
use Pimcore\Model\DataObject\Classificationstore\GroupConfig;
use Pimcore\Model\DataObject\Classificationstore\KeyConfig;
use Pimcore\Model\DataObject\Classificationstore\KeyGroupRelation;
use Pimcore\Model\DataObject\Classificationstore\StoreConfig;
use Pimcore\Model\DataObject\Concrete;

use function is_string;
use function method_exists;

class ClassificationstoreBuilder
{
    private ?Classificationstore $classificationstore = null;

    private ?int $storeId = null;

    /** @var array<string, int> */
    private array $keysNameIdMapping = [];

    /** @var array<string, int> */
    private array $groupNameIdMapping = [];

    /** @var array<string, int> */
    private array $collectionNameIdMapping = [];

    final protected function __construct()
    {
    }

    public static function createOrUpdate(string $fieldName, Concrete $owner, string $storeName, string $storeDescription): static
    {
        $builder = new static();

        $storeConfig = self::createOrUpdateStoreConfig($storeName, $storeDescription);
        $builder->storeId = $storeConfig->getId();
        if ($builder->storeId === null) {
            throw new LogicException('Store Config id has not been set');
        }

        $classificationStore = $owner->get($fieldName);
        if (!$classificationStore instanceof Classificationstore) {
            throw new LogicException("Classificationstore $fieldName not found in object");
        }
        $builder->classificationstore = $classificationStore;
        return $builder;
    }

    /**
     * @param array<array<string, bool|string>> $keys
     * @return $this
     */
    public function updateKeys(array $keys): static
    {
        foreach ($keys as $keyData) {
            if (!isset($keyData['name']) || !is_string($keyData['name'])) {
                throw new LogicException('Invalid key name');
            }

            $key = KeyConfig::getByName($keyData['name'], $this->getStoreId(), true);
            if ($key === null) {
                $key = new KeyConfig();
                $key->setStoreId($this->getStoreId());
                $key->setName($keyData['name']);
            }

            if (isset($keyData['title']) && is_string($keyData['title'])) {
                $key->setTitle($keyData['title']);
            }

            if (isset($keyData['description']) && is_string($keyData['description'])) {
                $key->setDescription($keyData['description']);
            }

            if (isset($keyData['enabled'])) {
                $key->setEnabled((bool)($keyData['enabled']));
            }

            if (isset($keyData['type']) && is_string($keyData['type'])) {
                $key->setType($keyData['type']);
            }

            if (isset($keyData['definition']) && is_string($keyData['definition'])) {
                $key->setDefinition($keyData['definition']); // JSON string
            }

            $key->save();

            $this->keysNameIdMapping[$key->getName()] = (int) $key->getId();
        }

        return $this;
    }

    /**
     * @param array<array<string, string>> $groups
     * @return $this
     */
    public function updateGroups(array $groups): static
    {
        foreach ($groups as $groupData) {
            if (!isset($groupData['name']) || !is_string($groupData['name'])) {
                throw new LogicException('Invalid group name');
            }

            $group = GroupConfig::getByName($groupData['name'], $this->getStoreId(), true);
            if ($group === null) {
                $group = new GroupConfig();
                $group->setStoreId($this->getStoreId());
                $group->setName($groupData['name']);
            }

            if (isset($groupData['description'])) {
                $group->setDescription($groupData['description']);
            }

            $group->save();

            $this->groupNameIdMapping[$group->getName()] = (int) $group->getId();
        }

        return $this;
    }

    /**
     * @param array<array<string, string>> $collections
     * @return $this
     */
    public function updateCollections(array $collections): static
    {
        foreach ($collections as $collectionData) {
            if (!isset($collectionData['name']) || !is_string($collectionData['name'])) {
                throw new LogicException('Invalid collection name');
            }

            $collection = CollectionConfig::getByName($collectionData['name'], $this->getStoreId(), true);
            if ($collection === null) {
                $collection = new CollectionConfig();
                $collection->setStoreId($this->getStoreId());
                $collection->setName($collectionData['name']);
            }

            if (isset($collectionData['description'])) {
                $collection->setDescription($collectionData['description']);
            }

            $collection->save();

            $this->collectionNameIdMapping[$collection->getName()] = (int) $collection->getId();
        }

        return $this;
    }

    /**
     * @param array<array<string, string|int|bool>> $relations
     * @return $this
     */
    public function updateKeyGroupRelations(array $relations): static
    {
        foreach ($relations as $relationData) {
            if (!isset($relationData['group_name']) && !is_string($relationData['group_name'])) {
                throw new LogicException('Invalid group_name');
            }

            if (!isset($relationData['key_name']) && !is_string($relationData['key_name'])) {
                throw new LogicException('Invalid key_name');
            }

            $groupId = $this->groupNameIdMapping[$relationData['group_name']] ?? null;
            $keyId = $this->keysNameIdMapping[$relationData['key_name']] ?? null;
            if (!$groupId || !$keyId) {
                throw new LogicException('Could not create group-key relation');
            }

            $relation = KeyGroupRelation::getByGroupAndKeyId($groupId, $keyId);
            if ($relation === null) {
                $relation = new KeyGroupRelation();
                $relation->setGroupId($groupId);
                $relation->setKeyId($keyId);
            }

            // depends on pimcore version
            // @phpstan-ignore-next-line function.alreadyNarrowedType
            if (method_exists($relation, 'setSorter') && isset($relationData['sorter'])) {
                $relation->setSorter((int)$relationData['sorter']);
            }

            // depends on pimcore version
            // @phpstan-ignore-next-line function.alreadyNarrowedType
            if (method_exists($relation, 'setMandatory') && isset($relationData['mandatory'])) {
                $relation->setMandatory((bool)$relationData['mandatory']);
            }

            $relation->save();
        }

        return $this;
    }

    /**
     * @param array<array<string, string|int>> $relations
     * @return $this
     */
    public function updateGroupCollectionRelations(array $relations): static
    {
        foreach ($relations as $relationData) {
            if (!isset($relationData['group_name']) && !is_string($relationData['group_name'])) {
                throw new LogicException('Invalid group_name');
            }

            if (!isset($relationData['collection_name']) && !is_string($relationData['collection_name'])) {
                throw new LogicException('Invalid collection_name');
            }

            $groupId = $this->groupNameIdMapping[$relationData['group_name']] ?? null;
            $collectionId = $this->collectionNameIdMapping[$relationData['collection_name']] ?? null;
            if (!$groupId || !$collectionId) {
                throw new LogicException('Could not create collection-group relation');
            }

            $relation = CollectionGroupRelation::getByGroupAndColId($groupId, $collectionId);
            if ($relation === null) {
                $relation = new CollectionGroupRelation();
                $relation->setGroupId($groupId);
                $relation->setColId($collectionId);
            }

            // depends on pimcore version
            // @phpstan-ignore-next-line function.alreadyNarrowedType
            if (method_exists($relation, 'setSorter') && isset($relationData['sorter'])) {
                $relation->setSorter((int)$relationData['sorter']);
            }

            $relation->save();
        }

        return $this;
    }

    /**
     * @param array<int, array<int, array<string, mixed>>> $items
     * @return $this
     * @throws Exception
     */
    public function setItems(array $items): static
    {
        foreach ($items as $groupId => $keys) {
            foreach ($keys as $keyId => $languages) {
                foreach ($languages as $language => $value) {
                    $this->getObject()->setLocalizedKeyValue($groupId, $keyId, $value, $language);
                }
            }
        }
        $this->getObject()->save();
        return $this;
    }

    /**
     * @param array<int, bool> $activeGroups
     * @return $this
     */
    public function setActiveGroups(array $activeGroups): static
    {
        $this->getObject()->setActiveGroups($activeGroups);
        $this->getObject()->save();
        return $this;
    }

    /**
     * @param array<int, int> $groupCollectionMapping
     * @return $this
     */
    public function setGroupCollectionMapping(array $groupCollectionMapping): static
    {
        $this->getObject()->setGroupCollectionMappings($groupCollectionMapping);
        $this->getObject()->save();
        return $this;
    }

    public function getObject(): Classificationstore
    {
        if (!$this->classificationstore instanceof Classificationstore) {
            throw new LogicException('Classificationstore object has not been set');
        }
        return $this->classificationstore;
    }

    /**
     * @param string $storeName
     * @param string $storeDescription
     * @return StoreConfig
     */
    private static function createOrUpdateStoreConfig(string $storeName, string $storeDescription): StoreConfig
    {
        $storeConfig = StoreConfig::getByName($storeName);
        if (!$storeConfig instanceof StoreConfig) {
            $storeConfig = new StoreConfig();
            $storeConfig->setName($storeName);
        }
        $storeConfig->setDescription($storeDescription);
        $storeConfig->save();
        return $storeConfig;
    }

    private function getStoreId(): int
    {
        if ($this->storeId === null) {
            throw new LogicException('Classificationstore object has not been set');
        }
        return $this->storeId;
    }
}
