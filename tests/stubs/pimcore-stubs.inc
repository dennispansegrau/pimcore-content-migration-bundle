<?php

declare(strict_types=1);

namespace Pimcore\Model\Element;

if (!\interface_exists(ElementInterface::class, false)) {
    interface ElementInterface
    {
        public function getId(): ?int;

        public function getType(): string;

        public function getFullPath(): string;

        public function setKey(string $key);

        public function getKey(): string;

        public function getRealFullPath(): string;

        public function clearDependentCache(): void;

        public static function getById(int $id): ?ElementInterface;

        public function save(array $parameters = []);

        public function getRealPath(): string;

        public function delete(): void;

        public function setParent(mixed $parent);

        public function setType(string $type);

        public static function getTypes(): array;
    }
}

if (!\class_exists(AbstractElement::class, false)) {
    abstract class AbstractElement implements ElementInterface
    {
        private string $key = '';

        public function __construct(
            private ?int $id = null,
            private string $type = 'element',
            private string $fullPath = '/',
        ) {
        }

        public function getId(): ?int
        {
            return $this->id;
        }

        public function getType(): string
        {
            return $this->type;
        }

        public function getFullPath(): string
        {
            return $this->fullPath;
        }

        public function setKey(string $key)
        {
            $this->key = $key;

            return $this;
        }

        public function getKey(): string
        {
            return $this->key;
        }

        public function getRealFullPath(): string
        {
            return $this->fullPath;
        }

        public function clearDependentCache(): void
        {
        }

        public static function getById(int $id): ?ElementInterface
        {
            return null;
        }

        public function save(array $parameters = [])
        {
            return $this;
        }

        public function getRealPath(): string
        {
            return $this->fullPath;
        }

        public function delete(): void
        {
        }

        public function setParent(mixed $parent)
        {
            return $this;
        }

        public function setType(string $type)
        {
            $this->type = $type;

            return $this;
        }

        public static function getTypes(): array
        {
            return [];
        }
    }
}

namespace Pimcore\Model;

use Pimcore\Model\Element\AbstractElement;

if (!\class_exists(Document::class, false)) {
    class Document extends AbstractElement
    {
        /** @var array<int, self> */
        private static array $instances = [];

        public function __construct(?int $id = null, string $fullPath = '/')
        {
            parent::__construct($id, 'document', $fullPath);
        }

        public static function register(self $document): void
        {
            if ($document->getId() !== null) {
                self::$instances[$document->getId()] = $document;
            }
        }

        public static function resetRegistry(): void
        {
            self::$instances = [];
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

        public static function getById(int $id): ?\Pimcore\Model\Element\ElementInterface
        {
            return self::$instances[$id] ?? null;
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
            return ['document'];
        }
    }
}

if (!\class_exists(Asset::class, false)) {
    class Asset extends AbstractElement
    {
        /** @var array<int, self> */
        private static array $instances = [];

        public function __construct(?int $id = null, string $fullPath = '/')
        {
            parent::__construct($id, 'asset', $fullPath);
        }

        public static function register(self $asset): void
        {
            if ($asset->getId() !== null) {
                self::$instances[$asset->getId()] = $asset;
            }
        }

        public static function resetRegistry(): void
        {
            self::$instances = [];
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

        public static function getById(int $id): ?\Pimcore\Model\Element\ElementInterface
        {
            return self::$instances[$id] ?? null;
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
            return ['asset'];
        }
    }
}

if (!\class_exists(DataObject::class, false)) {
    class DataObject extends AbstractElement
    {
        /** @var array<int, self> */
        private static array $instances = [];

        public function __construct(?int $id = null, string $fullPath = '/')
        {
            parent::__construct($id, 'object', $fullPath);
        }

        public static function register(self $object): void
        {
            if ($object->getId() !== null) {
                self::$instances[$object->getId()] = $object;
            }
        }

        public static function resetRegistry(): void
        {
            self::$instances = [];
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

        public static function getById(int $id): ?\Pimcore\Model\Element\ElementInterface
        {
            return self::$instances[$id] ?? null;
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
            return ['object'];
        }
    }
}

namespace Pimcore\Model\DataObject;

if (!\class_exists(Concrete::class, false)) {
    class Concrete extends \Pimcore\Model\DataObject
    {
        /** @var object|null */
        private object|null $classDefinition = null;

        public function setClass(object $classDefinition): void
        {
            $this->classDefinition = $classDefinition;
        }

        public function getClass(): ?object
        {
            return $this->classDefinition;
        }
    }
}

if (!\class_exists(Fieldcollection::class, false)) {
    class Fieldcollection
    {
    }
}

if (!\class_exists(ClassDefinition::class, false)) {
    class ClassDefinition
    {
        /**
         * @param array<string, object> $fieldDefinitions
         */
        public function __construct(private array $fieldDefinitions)
        {
        }

        /**
         * @return array<string, object>
         */
        public function getFieldDefinitions(): array
        {
            return $this->fieldDefinitions;
        }
    }
}

namespace Pimcore\Model\DataObject\ClassDefinition\Data;

if (!\class_exists(CalculatedValue::class, false)) {
    class CalculatedValue
    {
    }
}

if (!\class_exists(ReverseObjectRelation::class, false)) {
    class ReverseObjectRelation
    {
    }
}

namespace Pimcore\Model\Document;

if (!\class_exists(Editable::class, false)) {
    class Editable
    {
    }
}

namespace Pimcore\Model\Exception;

if (!\class_exists(NotFoundException::class, false)) {
    class NotFoundException extends \RuntimeException
    {
    }
}

namespace Doctrine\Migrations;

if (!\class_exists(DependencyFactory::class, false)) {
    class DependencyFactory
    {
        public function __construct(private object $configuration)
        {
        }

        public function getConfiguration(): object
        {
            return $this->configuration;
        }
    }
}

namespace Twig;

if (!\class_exists(Environment::class, false)) {
    class Environment
    {
        /**
         * @param array<string, string> $templates
         */
        public function __construct(private array $templates = [])
        {
        }

        /**
         * @param array<string, mixed> $context
         */
        public function render(string $template, array $context = []): string
        {
            return $this->templates[$template] ?? $template . ':' . \json_encode($context, JSON_THROW_ON_ERROR);
        }
    }
}
