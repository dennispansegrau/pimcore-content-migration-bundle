# Pimcore Content Migrations Bundle

The **Pimcore Content Migrations Bundle** adds a Doctrine-like migration system for **Documents**, **Assets**, and **Data Objects** to Pimcore.  
It allows developers to export Pimcore content structures as **executable migration scripts** and **rebuild environments from scratch** — enabling consistent and reproducible deployments across development, staging, and production systems.

---

## ✨ Features

- Generates migration scripts for Pimcore Documents, Assets, and Data Objects
- Rebuilds complete content structures on any environment
- Integrates with version control and deployment workflows
- Provides CLI commands similar to Doctrine Migrations (`diff`, `migrate`, `rollback`, `status`)

---

## 🧩 Example (CLI)

```bash
bin/console content:migration:create
```

## Setup
If you do not use doctrine migrations in your project yet, please add following to your config/config.yaml:
```
doctrine_migrations:
    migrations_paths:
        'App\Migrations': '%kernel.project_dir%/migrations'
```