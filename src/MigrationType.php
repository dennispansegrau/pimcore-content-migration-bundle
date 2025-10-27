<?php

namespace PimcoreContentMigration;

enum MigrationType: string
{
    case DOCUMENT = 'document';
    case ASSET = 'asset';
    case OBJECT = 'object';
}
