---
layout: default
title: Generation and response behavior
---

# Generation and response behavior

## Initialization

Run `php artisan magic:init` in a local application. The command resolves every destination before writing, treats unchanged reruns as no-ops, reports customized files as conflicts, and appends route includes only once.

Use `--dry-run` to inspect the plan. Use `--force` only after reviewing every reported conflict because it explicitly replaces conflicting generated files. Optional vendor integrations are not published automatically.

## Feature generation

`php artisan magic:model Invoice` uses the Standard profile by default. Every selected artifact is planned before the first write; a conflict prevents the complete plan, and newly written artifacts are rolled back after a write failure.

- `lean`: model, migration, factory, and focused model test.
- `repository`: Lean plus a repository contract, repository implementation, and an array/ID-based service returning typed `ResponseData`.
- `standard`: model, migration, contract, repository, service, controller, four requests, API route, factory, and feature test.
- `enterprise`: Standard plus a feature-specific service provider with repository binding and modular resource loading.

Standard and Repository safely maintain one shared `App\Providers\RepositoryServiceProvider`. Modern Laravel applications register it once in `bootstrap/providers.php`; recognized upgraded applications retaining the legacy bootstrap register it once in the canonical `config/app.php` provider list. Magic Make does not create an unused modern registry for a legacy application. Existing compatible binding lifecycles and unrelated provider code are preserved. Conflicting or ambiguous bindings and provider structures stop the complete plan even with `--force`. Repository-profile output has no controller, requests, route, or feature test; Enterprise retains its isolated provider behavior.

Use `--path` and `--namespace` for Composer PSR-4-aware modular output. Unsafe, mismatched, ambiguous, or unmapped targets fail during preflight.

## Response modes

Magic Make supports `view`, `json`, and `auto`. Resolution order is method override, controller property, then application configuration. Request negotiation is consulted only when the selected mode is `auto`.

The application default is `auto`; generated CRUD controllers explicitly select JSON to preserve their established API behavior. Services remain presentation-independent and return `ResponseData`.

For established applications, package updates and `magic:model` do not rewrite files previously created by `magic:init`. Read the [upgrade guide]({{ '/upgrade.html' | relative_url }}) before adopting newer initialization output.
