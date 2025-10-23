<?php

namespace DennisPansegrau\PimcoreContentMigrationBundle;

enum MigrationType: string
{
    case DOCUMENT = 'document';
    case ASSET = 'asset';
    case OBJECT = 'object';
}
