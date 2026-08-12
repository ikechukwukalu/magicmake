# Unreleased development direction

- Approved Lean, Standard, and Enterprise generation profiles with explicit artifact sets.
- Approved Composer PSR-4-aware target paths and namespace resolution with mismatch rejection.
- Approved modular placement for routes, providers, factories, migrations, and tests.

These items are not part of published v3.0.0 and require a separately authorized source synchronization and release.

## Unreleased

- Require PHP 8.2 or newer and Laravel 12 components.
- Test the package with Orchestra Testbench 10 and PHPUnit 11.
- Verify the test suite on PHP 8.2, 8.3, and 8.4.
- Stop forcing optional scaffolding integrations to be installed with the generator package.
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
