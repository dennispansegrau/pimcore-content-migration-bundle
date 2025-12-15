<?php

namespace PimcoreContentMigration\Twig\Extension;

use function array_key_exists;
use function get_class;
use function gettype;
use function in_array;

use InvalidArgumentException;

use function is_array;
use function is_bool;
use function is_callable;
use function is_float;
use function is_int;
use function is_numeric;
use function is_resource;
use function is_string;

use LogicException;
use Pimcore\Model\Document\Editable\Link;
use Pimcore\Model\Document\Editable\Pdf;
use Pimcore\Model\Document\Editable\Relation;
use Pimcore\Model\Document\Editable\Renderlet;
use Pimcore\Model\Document\Editable\Snippet;
use Pimcore\Model\Document\Editable\Video;
use Pimcore\Model\Document\Editable\Wysiwyg;
use Pimcore\Model\Element\AbstractElement;
use Pimcore\Model\Element\Data\MarkerHotspotItem;
use PimcoreContentMigration\Generator\Dependency\Dependency;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function str_repeat;
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
     * @param array<string, mixed> $parameters
     * Converts any value into a readable string.
     */
    public function valueToString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
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
        if ($value instanceof Renderlet || $value instanceof Relation) {
            $data = $value->getData();
            if (!is_array($data)) {
                return 'null';
            }
            $id = $data['id'] ?? null;
            $type = $data['type'] ?? null;
            if (empty($type) || empty($id) || !is_string($type) || !is_int($id)) {
                throw new LogicException('Invalid data.');
            }
            $dependency = $dependencyList->getByTypeAndId($type, $id);
            if ($dependency === null) {
                return (string) $id;
            }
            return '$' . $dependency->getVariableName() . '->getId()';
        }

        // Editable\Pdf
        if ($value instanceof Pdf) {
            $data = $value->getElement();
            if ($data === null) {
                return (string) $value->getId();
            }
            $dependency = $dependencyList->getByTypeAndId('asset', $data->getId() ?? 0);
            if ($dependency === null) {
                return (string) $value->getId();
            }
            return '$' . $dependency->getVariableName() . '->getId()';
        }

        // Editable\Snippet
        if ($value instanceof Snippet) {
            $data = $value->getSnippet();
            if ($data === null) {
                return (string) $value->getId();
            }
            $dependency = $dependencyList->getByTypeAndId('document', $value->getId());
            if ($dependency === null) {
                return (string) $value->getId();
            }
            return '$' . $dependency->getVariableName() . '->getId()';
        }

        // Editable\Snippet
        if ($value instanceof Wysiwyg) {
            $wysiwygDependencies = $value->resolveDependencies();
            $value = [];
            foreach ($wysiwygDependencies as $data) {
                if (!is_array($data) || !isset($data['type'], $data['id']) || !is_string($data['type']) || !is_int($data['id'])) {
                    continue;
                }
                $dependency = $dependencyList->getByTypeAndId($data['type'], $data['id']);
                if ($dependency === null) {
                    $value[$data['type']][$data['id']] = $data['id'];
                } else {
                    $value[$data['type']][$data['id']] = '$' . $dependency->getVariableName() . '->getId()';
                }
            }
            if (empty($value)) {
                return '[]';
            }
            $arrayString = "[\n";
            $indent = is_numeric($parameters['indent'] ?? null)
                ? (int) $parameters['indent']
                : 12;
            foreach ($value as $type => $item) {
                $arrayString .= str_repeat(' ', $indent + 4) . '\'' . $type . '\' => [' . "\n";
                foreach ($item as $oldId => $newId) {
                    $arrayString .= str_repeat(' ', $indent + 8) . $oldId . ' => ' . $newId . ",\n";
                }
                $arrayString .= str_repeat(' ', $indent + 4) . '],' . "\n";
            }
            $arrayString .= str_repeat(' ', $indent) . ']';
            return $arrayString;
        }

        // Editable\Video
        if ($value instanceof Video && array_key_exists('field', $parameters)) {
            $field = $parameters['field'] ?? null;
            if ($field === 'id') {
                $id = $value->getId();
            } elseif ($field === 'poster') {
                $id = $value->getPoster();
            } else {
                throw new InvalidArgumentException('Editable type video needs field parameter with value id or poster.');
            }
            if (is_int($id)) {
                $dependency = $dependencyList->getByTypeAndId('asset', $id);
                if ($dependency === null) {
                    return (string) $id;
                }
                return '$' . $dependency->getVariableName() . '->getId()';
            }
            $value = $id; // handle null or string later
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
            $indent = is_numeric($parameters['indent'] ?? null)
                ? (int) $parameters['indent']
                : 12;
            $arrayString = "[\n";
            foreach ($value as $key => $item) {
                $arrayString .= str_repeat(' ', $indent + 4) . '\'' . $key . '\' => ' . $this->valueToString($item, $dependencyList, ['indent' => $indent + 4]) . ",\n";
            }
            $arrayString .= str_repeat(' ', $indent) . ']';
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
