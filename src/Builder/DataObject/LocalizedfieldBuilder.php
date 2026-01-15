<?php

namespace PimcoreContentMigration\Builder\DataObject;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Localizedfield;

class LocalizedfieldBuilder
{
    private ?Localizedfield $localizedfield = null;

    final protected function __construct()
    {
    }

    /**
     * @param Concrete $owner
     * @return static
     * @throws \Exception
     */
    public static function create(Concrete $owner): static
    {
        $builder = new static();
        $builder->localizedfield = new Localizedfield();
        $builder->localizedfield->setObject($owner);
        return $builder;
    }

    public function getObject(): Localizedfield
    {
        if (!$this->localizedfield instanceof Localizedfield) {
            throw new \LogicException('Localizedfield object has not been set');
        }
        return $this->localizedfield;
    }

    /**
     * @param array<string, array<string, string>> $items
     * @return static
     * @throws \Exception
     */
    public function setLocalizedValues(array $items): static
    {
        foreach ($items as $language => $translations) {
            foreach ($translations as $name => $value) {
                $this->getObject()->setLocalizedValue($name, $value, $language);
            }
        }
        return $this;
    }
}
