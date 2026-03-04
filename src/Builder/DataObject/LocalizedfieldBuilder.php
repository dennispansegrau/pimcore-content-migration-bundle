<?php

namespace PimcoreContentMigration\Builder\DataObject;

use Exception;
use LogicException;

use function method_exists;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;
use Pimcore\Model\DataObject\Localizedfield;

class LocalizedfieldBuilder
{
    private ?Localizedfield $localizedfield = null;

    final protected function __construct()
    {
    }

    /**
     * @param Concrete|AbstractData $owner
     * @return static
     * @throws Exception
     */
    public static function create(Concrete|AbstractData $owner): static
    {
        $builder = new static();
        if (!method_exists($owner, 'getLocalizedfields')) {
            throw new Exception('Localizedfield not found in owner object');
        }
        $localizedField = $owner->getLocalizedfields();
        if (!$localizedField instanceof Localizedfield) {
            throw new Exception('Localizedfield not found in owner object');
        }
        $builder->localizedfield = $localizedField;
        return $builder;
    }

    public function getObject(): Localizedfield
    {
        if (!$this->localizedfield instanceof Localizedfield) {
            throw new LogicException('Localizedfield object has not been set');
        }
        return $this->localizedfield;
    }

    /**
     * @param array<string, array<string, string|null>> $items
     * @return static
     * @throws Exception
     */
    public function setLocalizedValues(array $items): static
    {
        foreach ($items as $language => $translations) {
            foreach ($translations as $name => $value) {
                if ($value === null) {
                    continue;
                }
                $this->getObject()->setLocalizedValue($name, $value, $language);
            }
        }
        return $this;
    }
}
