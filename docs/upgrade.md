---
layout: default
title: Upgrade guide
---

# Upgrade guide

## Established applications

Updating Magic Make does not rewrite application files previously created by `magic:init`. An established application may continue using only `magic:model`; its existing helpers, base controller, `ResponseData`, services, and response behavior remain unchanged.

Before updating, confirm that the application uses a combination in the [compatibility policy]({{ '/compatibility.html' | relative_url }}). Laravel 8–10 and PHP versions below 8.2 are not supported by v5.1.0. Laravel 11 is retained for compatibility evidence but has active advisories and is not security-supported; do not disable Composer security policy for a production update.

v5.1.0 adds the Repository profile and makes Standard and Repository generation maintain a shared `App\Providers\RepositoryServiceProvider`. Before the first post-upgrade generation, commit application-owned files and confirm that `bootstrap/providers.php` is parseable and directly returns its provider array. Compatible existing bindings are preserved; conflicting or ambiguous bindings stop generation and cannot be overridden with `--force`.

## Safe package-only upgrade

1. Create an application feature branch and record the current package version and lockfile.
2. Confirm the application test suite passes before changing dependencies.
3. Review the dependency transition: optional generated-application integrations are no longer installed transitively and must be required by the application when used.
4. Update Magic Make without running `magic:init`.
5. Run the application tests and preview a disposable feature with `magic:model --dry-run`.
6. Generate a real feature only after reviewing the complete preflight plan.
7. Verify API requests with and without an explicit `Accept: application/json` header.

Use `--profile=repository` when a feature needs persistence and a presentation-independent service but no HTTP layer. Standard remains the default. Existing initialization scaffolds are not rewritten by the package update or by `magic:model`.

Newly generated CRUD controllers explicitly select JSON. With legacy initialized scaffolding, that property is inert and the existing base-controller/helper pipeline remains authoritative.

## Optional initialization migration

Do not use `magic:init --force` during an ordinary package update. It can replace application-owned scaffolding. Instead, run `magic:init --dry-run`, compare each proposed file, port selected changes manually on an application migration branch, choose the response default explicitly, and retain application customizations.

## Rollback

Restore the previous Composer constraint and application lockfile, run `composer install`, clear application caches, and redeploy the last validated application commit. Generated application files are not automatically removed; revert any newly generated feature through normal source control.

The repository copy of [UPGRADE.md](https://github.com/ikechukwukalu/magicmake/blob/main/UPGRADE.md) remains the operational source for the selected branch or tag.
