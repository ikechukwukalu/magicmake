# Release Checklist

This checklist governs preparation and release of v5.1.0. Completing a check does not itself authorize a merge, DTAP promotion, public synchronization, tag, GitHub release, package publication, or Pages deployment.

## Release identity

- Candidate version: `v5.1.0`
- Previous public version: `v5.0.0`
- Previous public commit: `b07303f62c8997fce62edc1d9c32badd905c7e39`
- Development source PR HEAD SHA: `<record before development review>`
- Development `test` merge SHA: `<record after approved development merge>`
- Public base SHA: `<record immediately before synchronization>`
- Public candidate branch: `release/v5.1.0-repository-profile`
- Public candidate PR HEAD SHA: `<record after synchronization and every correction>`
- Public `main` merge SHA: `<record after approved public merge>`
- Immutable `v5.1.0` tag SHA: `<record after separately authorized tag creation>`

Every placeholder must contain a full 40-character commit SHA before the stage that depends on it proceeds. If a branch advances, repeat its checks and update the recorded SHA; do not reuse evidence from an earlier head.

## Development source qualification

- [ ] Record included and excluded roadmap scope and confirm v5.1.0 contains only reviewed changes since v5.0.0.
- [ ] Confirm the development preparation pull request targets `test` and its recorded HEAD SHA matches GitHub.
- [ ] Confirm the workflow triggered for that exact PR HEAD—not only a local run, earlier commit, `main`, or manual run—is green.
- [ ] Confirm all ten advertised Laravel/PHP jobs pass on that PR HEAD.
- [ ] Confirm resolved Composer lockfiles are retained from every matrix job as dependency evidence.
- [ ] Confirm Composer validation and PHP lint pass on every matrix job.
- [ ] Confirm Laravel 12–13 dependency resolution and advisory audits pass strictly.
- [ ] Confirm Laravel 11 jobs report known advisories and remain explicitly compatibility-only.
- [ ] Confirm PHPUnit passes on every matrix job and PHPStan passes at the configured level.
- [ ] Confirm statement coverage remains at or above 55%.
- [ ] Confirm Lean, Repository, Standard, and Enterprise smoke generation passes.
- [ ] Confirm Standard and Repository provider creation, update, registration, idempotency, conflict, same-basename module, runtime resolution, source revalidation, atomic creation, and rollback regressions pass.
- [ ] Confirm Standard-plan performance remains within the two-second/100-plan budget.
- [ ] Confirm established-project `magic:model` backward-compatibility regression passes.
- [ ] Confirm the changelog contains only v5.1.0 Repository-profile/shared-autobinding changes and preserves v5.0.0 and earlier history.
- [ ] Confirm README, upgrade guidance, lifecycle wording, and Pages source match the qualified behavior without temporary candidate-status claims.
- [ ] Confirm the Jekyll build and rendered-site link validation pass on the exact PR HEAD.
- [ ] Record QA, review, CI URLs, known risks, and unresolved defects in Project Context.
- [ ] Obtain explicit approval before merging the development PR into `test`.

## Public candidate synchronization

- [ ] After the approved development merge, record the full development `test` merge SHA selected as the release source.
- [ ] Re-read the public `main` head immediately before branching and record it as the public base SHA.
- [ ] Create the public candidate branch from that exact public base; do not commit directly to public `main`.
- [ ] Synchronize only the selected nested package source, tests, and release documentation while preserving public-only workflows, repository configuration, and release history.
- [ ] Verify the public candidate file manifest and content against the selected development source, documenting every intentional public adaptation.
- [ ] Restore and run `Tests/PublicV4UpgradeTest.php` so the synchronized candidate proves compatibility with established v4-generated projects.
- [ ] Open a draft public pull request to `main`; record its full HEAD SHA after every change.
- [ ] Confirm the complete ten-job compatibility matrix, lint, PHPUnit, PHPStan, coverage, audits, public v4 upgrade regression, documentation build, and link checks pass for that exact public PR HEAD.
- [ ] Confirm the public README, changelog, upgrade guide, release checklist, and Pages source describe durable released behavior rather than temporary branch or publication status.
- [ ] Obtain explicit approval before merging the public candidate pull request.

## Post-merge and publication verification

- [ ] Confirm public `main` contains the approved public PR HEAD and record the resulting full merge SHA.
- [ ] Require a green post-merge workflow on that exact public `main` SHA; PR-only evidence is insufficient for release.
- [ ] Obtain the separate production authorization `Approve production release v5.1.0.` before creating a tag, GitHub release, package publication, or production change.
- [ ] Create immutable tag `v5.1.0` at the authorized, green public `main` SHA; never move or reuse the tag.
- [ ] Create the GitHub release from that exact tag and verify its target SHA.
- [ ] Verify the public package channel exposes v5.1.0 and that its source/dist reference resolves to the same tagged SHA.
- [ ] Verify the `v5.1.0` tag, GitHub release, package-channel metadata, and public `main` release commit all identify the same source.
- [ ] Validate installation in fresh supported Laravel applications using the published package, not a branch or local path repository.
- [ ] Publish or deploy GitHub Pages only under separate authorization, then verify the live documentation against the released behavior.
- [ ] Record immutable release evidence and final status in Project Context.

## Rollback and correction

- [ ] Before tagging, retain the prior public commit, v5.0.0 package constraint, lockfile, and application rollback procedure.
- [ ] If a candidate fails before tagging, stop publication, correct it on a new commit, rerun every affected gate, and update the recorded candidate SHA.
- [ ] If a defect is found after tagging or publication, do not move, delete, or reuse `v5.1.0`; prepare a separately authorized corrective patch release from the appropriate source commit.
- [ ] Document package constraint/lockfile restoration, cache clearing, redeployment, recovery ownership, and verification evidence.
- [ ] Preserve failed candidate and release evidence until the incident or corrective release is closed.
