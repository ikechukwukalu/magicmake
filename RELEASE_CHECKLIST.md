# Release Checklist

This checklist prepares the v5.1.0 public release candidate. It does not authorize merging into `main`, tagging, publication, or release.

## Candidate selection

- [ ] Confirm the candidate version is v5.1.0 and the previous public release is immutable v5.0.0.
- [ ] Record the exact development commit selected from `test`.
- [ ] Record included and excluded roadmap scope.
- [ ] Confirm no unreviewed public-package source changes are included.
- [ ] Obtain explicit DTAP promotion authorization before changing `staging` or `production`.

## Compatibility and quality

- [ ] All ten advertised Laravel/PHP CI jobs pass.
- [ ] The current candidate commit has a green `main` or manually dispatched workflow run.
- [ ] Resolved Composer lockfiles are retained from every matrix job for dependency evidence.
- [ ] Composer validation passes; Laravel 12–13 advisory audits pass strictly.
- [ ] Laravel 11 compatibility jobs report their known advisories without presenting the line as security-supported.
- [ ] PHP lint and PHPUnit pass on every matrix job.
- [ ] PHPStan passes at the configured level.
- [ ] Statement coverage remains at or above 55%.
- [ ] Lean, Repository, Standard, and Enterprise smoke generation passes.
- [ ] Standard and Repository provider creation, update, registration, idempotency, conflict, same-basename module, runtime resolution, and rollback regressions pass.
- [ ] Standard-plan performance remains within the two-second/100-plan budget.
- [ ] Established-project `magic:model` compatibility regression passes.

## Documentation and operations

- [ ] Requirements, upgrade guide, migration notes, and changelog match the candidate.
- [ ] The v5.1.0 changelog contains only Repository-profile and shared-autobinding changes; v5.0.0 and earlier history remain intact.
- [ ] GitHub Pages source validates; publication remains separately authorized.
- [ ] Known risks and upstream end-of-life status are recorded.
- [ ] QA/UAT evidence and unresolved defects are recorded in Project Context.
- [ ] The public synchronization diff is prepared but not executed.
- [ ] Release notes and version recommendation are reviewed.

## Rollback readiness

- [ ] Record the previous validated development, staging, production, and public-package commits.
- [ ] Confirm the prior package version and Composer lockfile can be restored.
- [ ] Confirm application cache-clear and redeployment steps.
- [ ] Define who may authorize rollback and who verifies recovery.
- [ ] Do not delete the failed candidate branch or evidence until the incident is closed.
