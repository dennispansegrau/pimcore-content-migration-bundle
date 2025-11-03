# Pimcore Content Migrations Bundle

The **Pimcore Content Migrations Bundle** introduces a migration system — similar to Doctrine Migrations — for **Documents**, **Assets**, and **Data Objects** in Pimcore.  
It allows developers to export Pimcore content structures as **executable PHP migration scripts** and **rebuild entire environments from scratch**, enabling consistent and reproducible deployments across development, staging, and production systems.

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
bin/console content:migration:create [TYPE] [ID] [NAMESPACE] [--with-children] [--no-dependencies] [--inline-wysiwyg]
```

| Name          | Description                                                                                                 |
| ------------- | ----------------------------------------------------------------------------------------------------------- |
| **TYPE**      | The Pimcore element type (`document`, `asset`, or `object`)                                                 |
| **ID**        | The ID of the Pimcore element to export                                                                     |
| **NAMESPACE** | The namespace for the generated migration class, as configured under `doctrine_migrations.migrations_paths` |

| Option              | Description                                                                                           |
| ------------------- | ----------------------------------------------------------------------------------------------------- |
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