---
layout: default
title: Magic Make Documentation
---

# Magic Make

Magic Make is an opinionated Laravel scaffolding package with safe initialization, transactional feature generation, modular profiles, and explicit response modes.

This documentation covers the v5.1.0 package contract. Release evidence and operational gates are maintained separately from the usage guidance.

## Start here

- [Compatibility and lifecycle policy]({{ '/compatibility.html' | relative_url }})
- [Generation and response behavior]({{ '/usage.html' | relative_url }})
- [Upgrade guidance]({{ '/upgrade.html' | relative_url }})
- [Release-candidate verification]({{ '/release-readiness.html' | relative_url }})

Install the currently published package with Composer:

```shell
composer require ikechukwukalu/magicmake
```

Review the [public repository README](https://github.com/ikechukwukalu/magicmake/blob/main/README.md) for the complete command reference. Installed behavior is determined by the selected Composer version.

v5.1.0 adds the Repository profile for features that need a repository contract, implementation, and presentation-independent service without HTTP scaffolding. Standard and Repository generation safely maintain one shared `App\Providers\RepositoryServiceProvider` and its single registration in `bootstrap/providers.php`.
