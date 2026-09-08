# Upgrade Guide

## v5.0.1 to v5.1.0

v5.1.0 corrects provider registration for upgraded Laravel 11–13 applications that retain the legacy bootstrap structure. When `bootstrap/providers.php` is absent, Magic Make now recognizes the canonical `ServiceProvider::defaultProviders()->merge([...])->toArray()` provider list in `config/app.php`, reuses or inserts `App\Providers\RepositoryServiceProvider::class` there, and does not create an unused modern registry.

The reported Laravel 12 structure is covered by regression tests. If `App\Providers\RepositoryServiceProvider::class` is already present in the canonical `config/app.php` list, it remains unchanged and Repository generation proceeds. Duplicate, dynamic, malformed, guarded, or ambiguous registration structures still fail before any feature artifact is written.

Commit `bootstrap/app.php`, `config/app.php`, and application provider files before generation. Magic Make revalidates the bootstrap decision and selected registry before commit and rolls back package-owned writes on failure.

## v5.0.0 to v5.1.0

The upgrade is additive. Standard remains the default, and Lean and Enterprise retain their existing artifact contracts. Existing generated application files are not rewritten merely by updating the package.

The Repository profile, first published in v5.0.1, generates exactly seven feature artifacts: model, migration, factory, model test, repository contract, repository implementation, and a presentation-independent service. The service accepts arrays and scalar IDs and returns typed `ResponseData` without depending on HTTP requests.

Standard and Repository generation now maintain a shared `App\Providers\RepositoryServiceProvider` and register it exactly once through the application's active provider registry. Modern Laravel 11–13 applications use `bootstrap/providers.php`; recognized upgraded applications retaining the legacy bootstrap use their canonical `config/app.php` provider merge list. Review the applicable application-owned files before the first post-upgrade generation. Provider creation and modification participate in preflight, dry-run, source revalidation, atomic creation, rollback, and idempotency. Compatible existing bindings are preserved; conflicting or ambiguous bindings stop generation and cannot be overridden with `--force`.

Before updating an established v5.0 application:

1. Commit the current application state, `composer.json`, and lockfile.
2. Confirm the active provider registry is parseable: a direct `bootstrap/providers.php` array for modern bootstraps, or the canonical `ServiceProvider::defaultProviders()->merge([...])->toArray()` entry in `config/app.php` for a retained legacy bootstrap.
3. Update the package without running `magic:init`.
4. Run the application test suite.
5. Preview a disposable Standard or Repository feature with `magic:model --dry-run`.
6. Review the shared provider and bootstrap actions before generating the feature.

Rollback uses the normal package-only procedure below: restore the previous constraint and lockfile, reinstall dependencies, clear caches, and revert any newly generated feature through source control.

## Established applications

Updating the Magic Make package does not rewrite application files that were previously created by `magic:init`. An established application may continue using only `magic:model`; its existing helpers, base controller, `ResponseData`, services, and response behavior remain unchanged.

Before updating, confirm that the application uses a supported combination:

| Laravel | PHP | Support tier |
| --- | --- | --- |
| 11 | 8.2–8.4 | Compatibility only; security support ended March 12, 2026 |
| 12 | 8.2–8.5 | Security fixes only through February 24, 2027 |
| 13 | 8.3–8.5 | Supported; security fixes through Q1 2028 |

Laravel 8–10 and PHP versions below 8.2 are not supported by v5.1.0. Do not update such an application until its framework and PHP runtime have been upgraded and tested.

Current Composer policy blocks all available Laravel 11 releases because of active security advisories. Magic Make tests Laravel 11 only to detect regressions for established projects; this is not a security-support promise. Do not disable Composer policy for a production update. Upgrade the application to Laravel 12 or 13 before adopting v5.1.0.

Laravel 12's general bug-fix window ended August 13, 2026, but upstream security fixes continue through February 24, 2027. Plan migration to Laravel 13 rather than treating Laravel 12 compatibility as an indefinite lifecycle promise.

## Safe package-only upgrade

1. Create an application feature branch and record the current package version and lockfile.
2. Confirm the application test suite passes before changing dependencies.
3. Update Magic Make without running `magic:init`.
4. Run the application tests and generate a disposable feature with `magic:model --dry-run`.
5. Generate a real feature only after reviewing the complete preflight plan.
6. Verify API requests both with and without an explicit `Accept: application/json` header.

Standard generation now safely maintains `App\Providers\RepositoryServiceProvider`; the Repository profile uses the same shared provider. Modern Laravel 11–13 bootstraps use `bootstrap/providers.php`, which Magic Make can create atomically when missing. Conservatively recognized upgraded bootstraps continue using `config/app.php`: an existing exact registration is left byte-for-byte unchanged, and an absent registration is inserted once into the canonical provider merge list. Dynamic, duplicate, unparseable, or ambiguous provider lists stop generation even with `--force`. Exact `$this->app` and zero-argument `app()` container bindings and registrations are idempotent; an existing exact `singleton`, `scoped`, conditional lifecycle variant, or directly constructed `instance` is preserved rather than replaced with `bind`. Commit application-owned provider files before generation so rollback and review remain straightforward.

Use `--profile=repository` when a feature needs persistence and a presentation-independent service but no controller, requests, route, or feature test. Its service accepts arrays and scalar IDs and continues returning typed `ResponseData`.

Newly generated CRUD controllers explicitly select JSON mode. With legacy initialized scaffolding, the property is inert and the existing base-controller/helper pipeline remains authoritative.

## Optional initialization migration

Do not use `magic:init --force` as part of an ordinary package update. It can replace application-owned scaffolding.

To adopt current initialization output:

1. Run `php artisan magic:init --dry-run` and save the conflict report.
2. Compare each proposed file with the application-owned version.
3. Port selected changes manually on a dedicated migration branch.
4. Add `config/magicmake.php` and choose the application response default explicitly.
5. Test method, controller, and application response-mode precedence.
6. Retain application customizations and reject unrelated scaffold replacements.

## Rollback

If a package-only update fails validation, restore the prior Composer constraint and lockfile, run `composer install`, clear application caches, and redeploy the last validated application commit. Generated application files are not automatically removed; revert any newly generated feature through normal source control.
