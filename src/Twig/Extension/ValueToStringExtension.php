<?php

namespace PimcoreContentMigration\Twig\Extension;

use Pimcore\Model\Document\Editable\Link;
use Pimcore\Model\Document\Editable\Renderlet;
use function get_class;
use function gettype;
use function in_array;

use InvalidArgumentException;

use function is_array;
use function is_bool;
use function is_callable;
use function is_float;
use function is_int;
use function is_resource;
use function is_string;

use LogicException;
use Pimcore\Model\Element\AbstractElement;
use Pimcore\Model\Element\Data\MarkerHotspotItem;
use PimcoreContentMigration\Generator\Dependency\Dependency;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function str_replace;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ValueToStringExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('pcmb_value_to_string', [$this, 'valueToString']),
        ];
    }

    /**
     * Converts any value into a readable string.
     */
    public function valueToString(mixed $value, DependencyList $dependencyList): string
    {
        // MarkerHotspotItem
        if ($value instanceof MarkerHotspotItem) {
            if (in_array($value->getType(), ['document', 'asset', 'object'], true)) {
                $id = $value->getValue();
                if (!is_int($id)) {
                    throw new LogicException('Invalid value type in MarkerHotspotItem. Integer expected.');
                }
                $dependency = $dependencyList->getByTypeAndId($value->getType(), $id);
                if ($dependency === null) {
                    return 'null';
                }
                return '$' . $dependency->getVariableName() . '->getId()';
            }

            $value = $value->getValue(); // bool or string
        }

        // Editable\Link
        if ($value instanceof Link) {
            $data = $value->getData();
            if (!is_array($data)) {
                return 'null';
            }
            $internalType = $data['internalType'] ?? null;
            $internalId = $data['internalId'] ?? null;
            if (empty($internalType) || empty($internalId) || !is_string($internalType) || !is_int($internalId)) {
                return 'null';
            }
            $dependency = $dependencyList->getByTypeAndId($internalType, $internalId);
            if ($dependency === null) {
                return 'null';
            }
            return '$' . $dependency->getVariableName() . '->getId()';
        }

        // Editable\Link
        if ($value instanceof Renderlet) {
            $data = $value->getData();
            if (!is_array($data)) {
                return 'null';
            }
            $id = $data['id'] ?? null;
            $type = $data['type'] ?? null;
            if (empty($type) || empty($id) || !is_string($type) || !is_int($id)) {
                throw new LogicException('Invalid renderlet data.');
            }
            $dependency = $dependencyList->getByTypeAndId($type, $id);
            if ($dependency === null) {
                return (string) $id;
            }
            return '$' . $dependency->getVariableName() . '->getId()';
        }

        // NULL
        if ($value === null) {
            return 'null';
        }

        // BOOLEAN
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        // INTEGER
        if (is_int($value)) {
            return (string) $value;
        }

        // FLOAT
        if (is_float($value)) {
            return (string) $value;
        }

        // STRING
        if (is_string($value)) {
            $value = str_replace('\'', '\\\'', $value);
            return '\'' . $value . '\'';
        }

        // ARRAY
        if (is_array($value)) {
            if (empty($value)) {
                return '[]';
            }
            $arrayString = "[\n";
            foreach ($value as $key => $item) {
                $arrayString .= '            \'' . $key . '\' => ' . $this->valueToString($item, $dependencyList) . ",\n";
            }
            $arrayString .= '        ]';
            return $arrayString;
        }

        // OBJECT
        if ($value instanceof AbstractElement) {
            $dependency = $dependencyList->getDependency($value);
            if (!$dependency instanceof Dependency) {
                // try to get the element by path if it is not in the dependency list
                return '\\' . get_class($value) . '::getByPath(\'' . $value->getFullPath() . '\')';
            }
            return '$' . $dependency->getVariableName();
        }

        // RESOURCE (z. B. Datei-Handle)
        if (is_resource($value)) {
            throw new InvalidArgumentException('Unsupported value type: ' . gettype($value));
        }

        // CALLABLE
        if (is_callable($value)) {
            throw new InvalidArgumentException('Unsupported value type: ' . gettype($value));
        }

        // EVERYTHING ELSE (should not happen)
        throw new InvalidArgumentException('Unsupported value type: ' . gettype($value));
    }
}
