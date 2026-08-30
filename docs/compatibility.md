---
layout: default
title: Compatibility and lifecycle policy
---

# Compatibility and lifecycle policy

Support is based on complete CI jobs, not dependency constraints alone.

| Laravel | PHP | Lifecycle classification |
| --- | --- | --- |
| 11 | 8.2, 8.3, 8.4 | Compatibility only; security support ended March 12, 2026 |
| 12 | 8.2, 8.3, 8.4, 8.5 | Supported; upstream security fixes through February 24, 2027 |
| 13 | 8.3, 8.4, 8.5 | Supported; upstream security fixes through Q1 2028 |

Laravel 11 jobs use Composer's compatibility-only resolution mode and report advisories without failing the job. This exception is CI evidence for established consumers, not production installation guidance. Laravel 12 and Laravel 13 retain strict dependency resolution and audit failure.

Laravel 12's general bug-fix window ended August 13, 2026, while its upstream security-fix window continues through February 24, 2027. Dates and PHP ranges come from the [official Laravel support policy](https://laravel.com/docs/13.x/releases).

The package declares `php: ^8.2` so Composer can resolve the proven matrix. That open-ended constraint does not establish support for unlisted future PHP releases. A new Laravel/PHP combination becomes supported only after its complete CI job passes and this table is deliberately updated.
