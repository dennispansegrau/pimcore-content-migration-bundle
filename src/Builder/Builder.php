<?php

namespace PimcoreContentMigration\Builder;

abstract class Builder
{
    final protected function __construct()
    {
    }

    abstract public function getObject(): object;
}
