# Changelog

## v5.1.0

- Fix Standard and Repository generation in upgraded Laravel 11–13 applications that retain the legacy bootstrap structure and register providers through the canonical `config/app.php` provider merge list.
- Detect the application's active provider registry before planning: modern applications use `bootstrap/providers.php`, while recognized legacy applications preserve or update `config/app.php` without creating an unused bootstrap registry.
- Reuse an existing exact `App\Providers\RepositoryServiceProvider::class` registration and safely insert it once when absent.
- Reject duplicate, dynamic, malformed, guarded, or otherwise ambiguous bootstrap/provider structures before any generated artifact is written, including when `--force` is used.
- Revalidate `bootstrap/app.php` and the selected registry immediately before commit so a concurrent application change aborts atomically with no partial generation.
- Add regression coverage for the reported upgraded Laravel 12 structure, modern and legacy creation/reuse, rollback, concurrent changes, and runtime repository-contract resolution.
- Preserve existing public APIs, profile output, Standard defaults, repository binding lifecycles, initialized helpers, and established-project behavior.

See [UPGRADE.md](UPGRADE.md) before updating an established v5.0.1 application.

## v5.0.1

- Add the Repository profile, which generates a model, migration, factory, model test, repository contract, repository implementation, and presentation-independent service.
- Generate the Repository service with array and scalar-ID inputs and typed `ResponseData` results, without controller, request, route, or feature-test dependencies.
- Make Standard and Repository generation create or update the shared `App\Providers\RepositoryServiceProvider` and register it exactly once in `bootstrap/providers.php` for applications using the modern Laravel bootstrap structure.
- Preserve existing compatible container lifecycles and unrelated provider content while blocking conflicting or ambiguous bindings, including when `--force` is used.
- Include provider and bootstrap changes in preflight, dry-run, source revalidation, atomic creation, rollback, idempotency, modular namespace, and same-basename domain protections.
- Preserve Standard as the default profile, Lean output, Enterprise feature-specific providers, existing public APIs, and established-project behavior.

The immutable v5.0.1 release is preserved at public commit `b12d90e94130329e4030df0d81f919b2d47f0392`. Its legacy-bootstrap provider-registration limitation is corrected in v5.1.0.

See [UPGRADE.md](UPGRADE.md) before updating an established v5.0 application.

## v5.0.0

### Compatibility and dependencies

- Require PHP 8.2 or newer, with support limited to the CI-proven Laravel/PHP combinations documented in the compatibility guide.
- Support Laravel 11 on PHP 8.2–8.4 for compatibility-only use, Laravel 12 on PHP 8.2–8.5, and Laravel 13 on PHP 8.3–8.5.
- Add Symfony 8 compatibility while retaining Symfony 7.
- Install only the dependencies required by the scaffolder. Generated-application integrations are now Composer suggestions and must be installed when selected; the unused RequirePin dependency was removed.

### Generation and responses

- Add transactional preflight plans, conflict reporting, explicit `--force`, `--dry-run`, rollback protection, duplicate-route prevention, and non-destructive unchanged reruns for initialization and composite model generation.
- Stop automatically publishing optional vendor integrations during initialization.
- Add Lean, Standard, and Enterprise profiles with Composer PSR-4-aware paths, namespaces, and modular artifact placement.
- Add deterministic `view`, `json`, and `auto` response modes with method, controller, and application precedence while retaining JSON behavior for generated CRUD controllers.
- Preserve established applications that previously ran `magic:init`: ordinary package updates and `magic:model` do not rewrite their initialized helpers or base controller.

### Quality and documentation

- Add a ten-job Laravel/PHP CI matrix, strict Laravel 12–13 dependency audits, visible compatibility-only Laravel 11 advisory reporting, PHPStan level 5, a 55% statement-coverage floor, clean-profile smoke tests, and a Standard-plan performance budget.
- Add current-main and manual CI execution, immutable action pins, retained dependency-resolution evidence, upgrade and rollback guidance, and a validated Pages-ready documentation build without enabling publication or deployment.
- Align lifecycle wording with Laravel's support policy and limit PHP support claims to CI-proven combinations.

See [UPGRADE.md](UPGRADE.md) before updating an established application.

## v4.0.0

- Require PHP 8.2 or newer and Laravel 12 components.
- Test the package with Orchestra Testbench 10 and PHPUnit 11.
- Verify the test suite on PHP 8.2, 8.3, and 8.4.
- Require the Laravel 12-compatible releases of ClamavFileUpload, MakeService, and RequirePin.
- Resolve stable dependency releases by default and remove the unused ReactPHP HTTP dependency.
- Allow API scaffolding in a fresh Laravel 12 application before `routes/api.php` exists.

## v3.0.0

- Added Laravel 12 component constraints and compatible dependency generations.
- The release declares Laravel 8 through Laravel 12 constraints; a complete compatibility matrix was not included in this release.

## v2.0.4

- Added highlighted text for email notification
- Add functionality to search child tables via advanced search

## v2.0.3

- Updated phone verification

## v2.0.2

- Fixed bugs

## v2.0.1

- Updated package to support Laravel view

## v2.0.0

- Updated package to support Laravel 11

## v1.0.1

- Optimized the code
- Fixed bugs
