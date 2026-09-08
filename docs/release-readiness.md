---
layout: default
title: Release verification
---

# Release verification

The public workflow runs ten Laravel/PHP jobs on pull requests, pushes to `main`, and manual dispatches. Every job validates package metadata, resolves the selected Laravel and Testbench versions, lints PHP, and runs PHPUnit.

Laravel 12 and Laravel 13 jobs strictly audit the resolved lockfile. Laravel 11 jobs preserve compatibility visibility while reporting their known advisories. The Laravel 13 / PHP 8.5 edge also runs PHPStan level 5 and enforces the 55% statement-coverage floor. Matrix lockfiles are uploaded for 14 days as dependency-resolution evidence.

The regression suite exercises clean Lean, Repository, Standard, and Enterprise generation, legacy initialized-application behavior, the public-v4 dependency transition, generated PHP syntax, and a budget requiring 100 Standard generation plans to complete within two seconds. Shared-provider coverage includes modern and upgraded-legacy registry detection, creation, update, registration, idempotency, ambiguous-structure rejection, same-basename modules, runtime resolution, bootstrap decision-source revalidation, concurrency aborts, and byte-for-byte rollback.

CI also performs a real Jekyll build and checks required documentation sources and rendered-site links. The workflow does not enable GitHub Pages or deploy the built site.

See the [release checklist](https://github.com/ikechukwukalu/magicmake/blob/main/RELEASE_CHECKLIST.md). Passing these checks is release evidence; it does not authorize merge, tagging, publication, release creation, Packagist publication, or Pages deployment.
