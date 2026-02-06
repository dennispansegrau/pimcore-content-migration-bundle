<?php

namespace PimcoreContentMigration\Builder\DataObject\QuantityValue;

use Pimcore\Model\DataObject\QuantityValue\Unit;

class UnitBuilder
{
    final protected function __construct()
    {
    }

    public static function findOrCreate(
        string $id,
        ?string $abbreviation = null,
        ?string $longName = null,
        null|string|Unit $baseUnit = null,
        ?float $factor = null,
        ?float $conversionOffset = null,
        ?string $converter = null,
    ): Unit {
        $unit = Unit::getById($id);
        if ($unit === null) {
            $unit = new Unit();
            $unit->setId($id);
            $unit->setAbbreviation($abbreviation);
            $unit->setLongname($longName);
            $unit->setBaseunit($baseUnit);
            $unit->setFactor($factor);
            $unit->setConversionOffset($conversionOffset);
            $unit->setConverter($converter);
            $unit->save();
        }
        return $unit;
    }
}
