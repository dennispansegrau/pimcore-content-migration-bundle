<?php

namespace Doctrine\Migrations;

use Doctrine\Migrations\Configuration\Configuration;

class DependencyFactory
{
    public function getConfiguration(): Configuration
    {
        return new Configuration();
    }
}
