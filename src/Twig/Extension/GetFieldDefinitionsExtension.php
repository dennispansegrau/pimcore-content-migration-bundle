<?php

namespace PimcoreContentMigration\Twig\Extension;

use function array_keys;

use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GetFieldDefinitionsExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('pcmb_get_object_fields', [$this, 'getObjectFields']),
        ];
    }

    /**
     * returns array<string, mixed>
     * Returns the field definitions of a data object.
     * @return array<string, mixed>
     */
    public function getObjectFields(AbstractData $value): array
    {
        $fields = array_keys($value->getDefinition()->getFieldDefinitions());
        $values = [];
        foreach ($fields as $field) {
            $values[(string) $field] = $value->get((string) $field);
        }
        return $values;
    }
}
