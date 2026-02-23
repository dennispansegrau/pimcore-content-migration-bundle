<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\DataObject;

use function array_key_exists;
use function array_map;
use function in_array;
use function is_string;

use LogicException;
use Pimcore\Model\DataObject\Classificationstore;
use Pimcore\Model\DataObject\Classificationstore\CollectionConfig;
use Pimcore\Model\DataObject\Classificationstore\CollectionGroupRelation;
use Pimcore\Model\DataObject\Classificationstore\GroupConfig;
use Pimcore\Model\DataObject\Classificationstore\KeyConfig;
use Pimcore\Model\DataObject\Classificationstore\KeyGroupRelation;
use Pimcore\Model\DataObject\Classificationstore\StoreConfig;
use PimcoreContentMigration\Builder\DataObject\ClassificationstoreBuilder;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\IndentTrait;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\ValueToStringConverterTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function sprintf;
use function str_repeat;

class ClassificationstoreStringifier implements ValueStringifier
{
    use ValueToStringConverterTrait;
    use IndentTrait;

    /** @var array<int, string> */
    private array $keysIdNameMapping = [];

    /** @var array<int, string> */
    private array $groupIdNameMapping = [];

    /** @var array<int, string> */
    private array $collectionIdNameMapping = [];

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof Classificationstore;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var Classificationstore $value */
        $builderName = ClassificationstoreBuilder::class;
        $owner = $this->getOwner($parameters);

        $storeId = $this->getStoreId($value);
        $storeConfig = $this->getStoreConfig($storeId);

        $spacing = str_repeat(' ', $this->getAndIncreaseIndent($parameters) + 4);

        // structure data
        $keysString = $this->getConverter()->convertValueToString($this->getKeys($storeId), $dependencyList, $parameters);
        $groupsString = $this->getConverter()->convertValueToString($this->getGroups($storeId), $dependencyList, $parameters);
        $collectionsString = $this->getConverter()->convertValueToString($this->getCollections($storeId), $dependencyList, $parameters);
        $keyGroupRelationsString = $this->getConverter()->convertValueToString($this->getKeyGroupRelations(), $dependencyList, $parameters);
        $groupCollectionRelationString = $this->getConverter()->convertValueToString($this->getGroupCollectionRelation(), $dependencyList, $parameters);

        // actual classification store data
        $itemsString = $this->getConverter()->convertValueToString($value->getItems(), $dependencyList, $parameters);
        $activeGroupString = $this->getConverter()->convertValueToString($this->getActiveGroups($value), $dependencyList, $parameters);
        $groupCollectionMappingString = $this->getConverter()->convertValueToString($this->getGroupCollectionMapping($value), $dependencyList, $parameters);

        return sprintf(
            "\\%s::createOrUpdate('%s', %s, '%s', '%s')\n" .
                "%s->updateKeys(%s)\n" .
                "%s->updateGroups(%s)\n" .
                "%s->updateCollections(%s)\n" .
                "%s->updateKeyGroupRelations(%s)\n" .
                "%s->updateGroupCollectionRelations(%s)\n" .
                "%s->setItems(%s)\n" .
                "%s->setActiveGroups(%s)\n" .
                "%s->setGroupCollectionMapping(%s)\n" .
                '%s->getObject()',
            $builderName,
            $value->getFieldname(),
            $owner,
            $storeConfig->getName() ?? '',
            $storeConfig->getDescription() ?? '',
            $spacing,
            $keysString,
            $spacing,
            $groupsString,
            $spacing,
            $collectionsString,
            $spacing,
            $keyGroupRelationsString,
            $spacing,
            $groupCollectionRelationString,
            $spacing,
            $itemsString,
            $spacing,
            $activeGroupString,
            $spacing,
            $groupCollectionMappingString,
            $spacing,
        );
    }

    /**
     * @param int $storeId
     * @return array<array<string, bool|string>>
     */
    public function getKeys(int $storeId): array
    {
        $keys = (new KeyConfig\Listing())->setCondition('storeId = ?', [$storeId])->load();

        foreach ($keys as $key) {
            if (in_array($key->getName(), $this->keysIdNameMapping, true)) {
                throw new LogicException(sprintf('The Key "%s" in the Classification Store %d is not unique. No mapping possible.', $key->getName(), $storeId));
            }
            $this->keysIdNameMapping[(int) $key->getId()] = $key->getName();
        }

        return array_map(fn ($key) => [
            'name' => $key->getName(),
            'title' => $key->getTitle() ?? '',
            'description' => $key->getDescription() ?? '',
            'enabled' => $key->getEnabled(),
            'type' => $key->getType(),
            'definition' => $key->getDefinition(), // JSON string (FieldDefinition)
        ], $keys);
    }

    /**
     * @param int $storeId
     * @return array<array<string, string>>
     */
    public function getGroups(int $storeId): array
    {
        $groups = (new GroupConfig\Listing())->setCondition('storeId = ?', [$storeId])->load();

        foreach ($groups as $group) {
            if (in_array($group->getName(), $this->groupIdNameMapping, true)) {
                throw new LogicException(sprintf('The Group "%s" in the Classification Store %d is not unique. No mapping possible.', $group->getName(), $storeId));
            }
            $this->groupIdNameMapping[(int) $group->getId()] = $group->getName();
        }

        return array_map(fn ($group) => [
            'name' => $group->getName(),
            'description' => $group->getDescription() ?? '',
        ], $groups);
    }

    /**
     * @param int $storeId
     * @return array<array<string, string>>
     */
    public function getCollections(int $storeId): array
    {
        $collectionConfigs = (new CollectionConfig\Listing())->setCondition('storeId = ?', [$storeId])->load();

        foreach ($collectionConfigs as $collectionConfig) {
            if (in_array($collectionConfig->getName(), $this->collectionIdNameMapping, true)) {
                throw new LogicException(sprintf('The Group Collection "%s" in the Classification Store %d is not unique. No mapping possible.', $collectionConfig->getName(), $storeId));
            }
            $this->collectionIdNameMapping[(int) $collectionConfig->getId()] = $collectionConfig->getName();
        }

        return array_map(fn ($collectionConfig) => [
            'name' => $collectionConfig->getName(),
            'description' => $collectionConfig->getDescription(),
        ], $collectionConfigs);
    }

    /**
     * @return array<array<string, string|int|bool>>
     */
    public function getKeyGroupRelations(): array
    {
        $keyGroupRels = (new KeyGroupRelation\Listing())->load();
        $keyGroupRelations = [];

        // Key ↔ Group
        foreach ($keyGroupRels as $relation) {
            if (!isset($this->groupIdNameMapping[$relation->getGroupId()]) ||
                !isset($this->keysIdNameMapping[$relation->getKeyId()])) {
                continue; // does not belong to store id
            }

            $groupName = $this->groupIdNameMapping[$relation->getGroupId()];
            $keyName = $this->keysIdNameMapping[$relation->getKeyId()];

            $keyGroupRelations[] = [
                'group_name' => $groupName,
                'key_name' => $keyName,
                'sorter' => $relation->getSorter(),
                'mandatory' => $relation->isMandatory(),
            ];
        }

        return $keyGroupRelations;
    }

    /**
     * @return array<array<string, string|int>>
     */
    public function getGroupCollectionRelation(): array
    {
        $colGroupRels = (new CollectionGroupRelation\Listing())->load();
        $collectionGroupRelations = [];

        foreach ($colGroupRels as $relation) {
            if (!isset($this->groupIdNameMapping[$relation->getGroupId()]) ||
                !isset($this->collectionIdNameMapping[$relation->getColId()])) {
                continue; // does not belong to store id
            }

            $collectionName = $this->collectionIdNameMapping[$relation->getColId()];
            $groupName = $this->groupIdNameMapping[$relation->getGroupId()];

            $collectionGroupRelations[] = [
                'collection_name' => $collectionName,
                'group_name' => $groupName,
                'sorter' => $relation->getSorter(),
            ];
        }

        return $collectionGroupRelations;
    }

    /**
     * @param Classificationstore $classificationStore
     * @return array<int, int>
     */
    public function getGroupCollectionMapping(Classificationstore $classificationStore): array
    {
        $groupCollectionMapping = [];
        foreach ($classificationStore->getActiveGroups() as $groupId => $value) {
            $collectionId = $classificationStore->getGroupCollectionMapping((int) $groupId);
            if ($collectionId !== null) {
                $groupCollectionMapping[$groupId] = $collectionId;
            }
        }
        return $groupCollectionMapping;
    }

    /**
     * @param array<string, mixed> $parameters
     * @return string
     */
    private function getOwner(array $parameters): string
    {
        $owner = '$builder->getObject()';
        if (array_key_exists('owner', $parameters) &&
            is_string($parameters['owner'])) {
            $owner = $parameters['owner'];
        }
        return $owner;
    }

    /**
     * @param Classificationstore $classificationStore
     * @return array<int, bool>
     */
    public function getActiveGroups(Classificationstore $classificationStore): array
    {
        return $classificationStore->getActiveGroups();
    }

    /**
     * @param Classificationstore $classificationStore
     * @return int
     */
    private function getStoreId(Classificationstore $classificationStore): int
    {
        $classDefinition = $classificationStore->getClass();
        $fieldDefinition = $classDefinition?->getFieldDefinition($classificationStore->getFieldname());
        if (!$fieldDefinition instanceof \Pimcore\Model\DataObject\ClassDefinition\Data\Classificationstore) {
            throw new LogicException('FieldDefinition expected.');
        }
        return $fieldDefinition->getStoreId();
    }

    /**
     * @param int $storeId
     * @return StoreConfig
     */
    private function getStoreConfig(int $storeId): StoreConfig
    {
        $storeConfig = StoreConfig::getById($storeId);
        if (!$storeConfig instanceof StoreConfig) {
            throw new LogicException(sprintf('StoreConfig with id %d not found', $storeId));
        }
        return $storeConfig;
    }
}
