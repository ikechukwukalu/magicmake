# Release Checklist

This checklist validates the public v5.0.0 release candidate. Completing it does not authorize merging, tagging, publication, GitHub release creation, Packagist publication, or Pages deployment.

## Candidate selection

- [ ] Record development implementation head `259060f0a01f3a362c1b76e41053964623df70e0`, merge-to-`test` commit `01cc4446aa7b50ed1ca54c38d15c42778716e438`, merge-to-`main` commit `d8247b04936f2cf65dbc02b301b56b5eb6747546`, and user changelog commit `de827cf7ee03e15608976f6e60cc5c1895220fb0`.
- [ ] Record public synchronization base `c246e7aff03fce379e8a14baa52730f32e5f2db2` and the exact candidate commit.
- [ ] Record included and excluded roadmap scope.
- [ ] Confirm no unreviewed public-package source changes are included.
- [ ] Confirm tracked `.DS_Store`, dependency locks, caches, build output, and vendor files are absent from the candidate diff.

## Compatibility and quality

- [ ] All ten advertised Laravel/PHP CI jobs pass.
- [ ] The current public candidate commit has a green pull-request workflow run.
- [ ] Resolved Composer lockfiles are retained from every matrix job for dependency evidence.
- [ ] Composer validation passes; Laravel 12–13 advisory audits pass strictly.
- [ ] Laravel 11 compatibility jobs report their known advisories without presenting the line as security-supported.
- [ ] PHP lint and PHPUnit pass on every matrix job.
- [ ] PHPStan passes at the configured level.
- [ ] Statement coverage remains at or above 55%.
- [ ] Lean, Standard, and Enterprise smoke generation passes.
- [ ] Standard-plan performance remains within the two-second/100-plan budget.
- [ ] Established-project `magic:model` compatibility regression passes.
- [ ] The public-v4 dependency-transition regression passes and all moved integration dependencies remain documented as suggestions.

## Documentation and operations

- [ ] Requirements, upgrade guide, migration notes, and changelog match the candidate.
- [ ] GitHub Pages source validates; publication remains separately authorized.
- [ ] Known risks and upstream end-of-life status are recorded.
- [ ] The synchronization provenance and unresolved defects are recorded in the draft pull request.
- [ ] Release notes and the v5.0.0 version are reviewed for SemVer accuracy.

## Rollback readiness

- [ ] Record previous public `main` commit `c246e7aff03fce379e8a14baa52730f32e5f2db2` and Packagist's published v4.0.0 source commit `c090da47e9af88b2951f670d115b1f5de9a02f61`.
- [ ] Confirm the prior package version and Composer lockfile can be restored.
- [ ] Confirm application cache-clear and redeployment steps.
- [ ] Define who may authorize rollback and who verifies recovery.
- [ ] Do not delete the failed candidate branch or evidence until the incident is closed.

## Separate release authorization

- [ ] Obtain explicit authorization before merging this preparation pull request.
- [ ] After merge and only with explicit release authorization, create a new immutable `v5.0.0` tag; never move or reuse an existing tag.
- [ ] Verify the GitHub release and Packagist both resolve the new version to the same immutable commit.
