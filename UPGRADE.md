# Upgrade Guide

## v5.0.0 to v5.1.0

v5.1.0 is currently a public release candidate; it has not yet been merged into `main`, tagged, published, or released.

The upgrade is additive. Standard remains the default, and Lean and Enterprise retain their existing artifact contracts. Existing generated application files are not rewritten merely by updating the package.

The new Repository profile generates exactly seven feature artifacts: model, migration, factory, model test, repository contract, repository implementation, and a presentation-independent service. The service accepts arrays and scalar IDs and returns typed `ResponseData` without depending on HTTP requests.

Standard and Repository generation now maintain a shared `App\Providers\RepositoryServiceProvider` and register it exactly once in `bootstrap/providers.php`. Review both application-owned files before the first post-upgrade generation. Provider creation and modification participate in preflight, dry-run, source revalidation, atomic creation, rollback, and idempotency. Compatible existing bindings are preserved; conflicting or ambiguous bindings stop generation and cannot be overridden with `--force`.

Before updating an established v5.0 application:

1. Commit the current application state, `composer.json`, and lockfile.
2. Confirm `bootstrap/providers.php` is parseable and returns a direct provider array.
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

Laravel 8–10 and PHP versions below 8.2 are not part of the v5.1.0 release-candidate contract. Do not update such an application until its framework and PHP runtime have been upgraded and tested.

Current Composer policy blocks all available Laravel 11 releases because of active security advisories. Magic Make tests Laravel 11 only to detect regressions for established projects; this is not a security-support promise. Do not disable Composer policy for a production update. Upgrade the application to Laravel 12 or 13 before adopting the v5.1.0 release candidate.

Laravel 12's general bug-fix window ended August 13, 2026, but upstream security fixes continue through February 24, 2027. Plan migration to Laravel 13 rather than treating Laravel 12 compatibility as an indefinite lifecycle promise.

## Safe package-only upgrade

1. Create an application feature branch and record the current package version and lockfile.
2. Confirm the application test suite passes before changing dependencies.
3. Update Magic Make without running `magic:init`.
4. Run the application tests and generate a disposable feature with `magic:model --dry-run`.
5. Generate a real feature only after reviewing the complete preflight plan.
6. Verify API requests both with and without an explicit `Accept: application/json` header.

Standard generation now safely maintains `App\Providers\RepositoryServiceProvider` and its registration in `bootstrap/providers.php`. The new Repository profile uses the same shared provider. Ensure the Laravel 11–13 application has a parseable `bootstrap/providers.php` returning an array before generation. Exact `$this->app` and zero-argument `app()` container bindings and registrations are idempotent; an existing exact `singleton`, `scoped`, conditional lifecycle variant, or directly constructed `instance` is preserved rather than replaced with `bind`. Conflicting or ambiguous implementations and structurally unsafe files stop generation even with `--force`. Commit application-owned provider files before generation so rollback and review remain straightforward.

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
