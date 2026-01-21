# Pimcore Content Migrations Bundle

The **Pimcore Content Migrations Bundle** introduces a migration system — similar to Doctrine Migrations — for **Documents**, **Assets**, and **Data Objects** in Pimcore.  
It allows developers to export Pimcore content structures as **executable PHP migration scripts** and **rebuild entire environments from scratch**, enabling consistent and reproducible deployments across development, staging, and production systems.  
The generated migrations create only dummy dependencies — documents, assets, and data objects are included with their content, but without their dependencies or child elements.

---

## 📚 Table of Contents
- [✨ Features](#-features)
- [⚙️ Installation](#-installation)
- [🧩 CLI Command](#-cli-command)
- [💻 Example](#-example)
- [🧰 Configuration](#-configuration)
- [🧩 Custom DataType handlers](#-custom-datatype-handlers)
- [🎨 Custom Twig templates](#-custom-twig-templates)
- [🧠 Notes](#-notes)
- [🧾 License](#-license)

---

## ✨ Features

- Generates migration scripts for Pimcore Documents, Assets, and Data Objects
- Rebuilds complete content structures on any environment
- Integrates with version control and deployment workflows

---

## ⚙️ Installation

Install the bundle via Composer:

```bash
composer require dennispansegrau/pimcore-content-migrations-bundle
```
Then enable it in your Pimcore project:
```bash
bin/console pimcore:bundle:enable PimcoreContentMigrationBundle
```
(Optional) Clear and rebuild your cache:
```bash
bin/console cache:clear
```

---

## 🧩 CLI Command

```
bin/console content:migration:create [TYPE] [ID] [--namespace=...] [--with-children] [--no-dependencies] [--inline-wysiwyg]
```

| Name          | Description                                                                                                 |
| ------------- | ----------------------------------------------------------------------------------------------------------- |
| **TYPE**      | The Pimcore element type (`document`, `asset`, or `object`)                                                 |
| **ID**        | The ID of the Pimcore element to export                                                                     |

| Option              | Description                                                                                           |
| ------------------- | ----------------------------------------------------------------------------------------------------- |
| `--namespace`       | The namespace for the generated migration class (falls back to `pimcore_content_migration.default_namespace` if set) |
| `--with-children`   | Include all child elements (e.g., sub-documents or child objects) in the migration file               |
| `--no-dependencies` | Exclude related dependencies (e.g., linked assets or objects) from the migration                      |
| `--inline-wysiwyg`  | Inline WYSIWYG field content directly into the migration instead of saving it in a separate HTML file |

---

## 💻 Example
```bash
bin/console content:migration:create document 1 --namespace=App\\Migrations\\Content
```
This command creates a migration for the document with ID 1 and stores it in the namespace App\Migrations\Content.

---

## 🧰 Configuration
If you do not use doctrine migrations in your project yet, please add following to your config/config.yaml:
```
doctrine_migrations:
    migrations_paths:
        'App\Migrations\Content': '%kernel.project_dir%/migrations/content'
```
This defines where generated content migration files are stored.

You can also set a default namespace to avoid passing it every time:
```
pimcore_content_migration:
    default_namespace: 'App\Migrations\Content'
```
When set, `content:migration:create` uses this namespace if none is provided.

---

## 🧩 Custom DataType handlers
If you use custom DataTypes (or hit "Unsupported object of class" errors), you can register your own
stringifier handler to control how values are serialized in migrations.

Example handler:
```php
<?php

namespace App\ContentMigration\Stringifier;

use App\Model\DataObject\Data\MyCustomDataType;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\ValueToStringConverterTrait;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

final class MyCustomDataTypeStringifier implements ValueStringifier
{
    use ValueToStringConverterTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof MyCustomDataType;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        $data = $this->getConverter()->convertValueToString($value->getData(), $dependencyList, $parameters);

        return sprintf(
            'new \\App\\Model\\DataObject\\Data\\MyCustomDataType(%s)',
            $data
        );
    }
}
```

Register it as a service (priority can be adjusted to run before the default handler):
```yaml
# config/services.yaml
services:
    App\ContentMigration\Stringifier\MyCustomDataTypeStringifier:
        tags:
            - { name: 'pcmb.stringifier_handler', priority: 100 }
```

---

## 🎨 Custom Twig templates
You can override the bundled Twig templates to adapt the generated migration code to your own style.
The bundle reads template paths from configuration, so you can point to your own templates.

Example configuration:
```yaml
# config/config.yaml
pimcore_content_migration:
    templates:
        migration_template: '@App/content_migration/migration.php.twig'
        document_template: '@App/content_migration/document.php.twig'
        asset_template: '@App/content_migration/asset.php.twig'
        object_template: '@App/content_migration/object.php.twig'
```

When customizing templates, use the Twig helper `pcmb_value_to_string` to safely serialize Pimcore
objects and complex values into PHP code:
```twig
{{ pcmb_value_to_string(myValue, dependencies) }}
```

---

## 🧠 Notes

- Migrations are executable PHP scripts — they can be committed to your VCS and deployed alongside your code.
- Each migration is idempotent and can safely be executed multiple times.
- This bundle does not modify Pimcore’s database schema — it only manages content structures.
- You can organize different types of migrations (Documents, Assets, Objects) under separate namespaces if needed.

---

## 🧾 License

This bundle is a commercial product.  
For licensing information, subscription options, or support inquiries, please contact the author.
