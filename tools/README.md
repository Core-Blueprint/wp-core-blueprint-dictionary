# Development tools

## Conformance

Run `php tools/conformance.php`.

The check verifies expected runtime files, public Base boundaries, native CPT/taxonomy/meta structure, the machine-owned alphabet contract, Governance APIs and configurable rewrites.

## PHP syntax

```bash
find . -type f -name '*.php' -not -path './build/*' -print0 | xargs -0 -n1 php -l
```

## Release build

Run `tools/build-release`.

The builder detects the version, stages one canonical `core-blueprint-dictionary/` root, lints PHP, runs conformance and creates `build/core-blueprint-dictionary-{version}.zip`.
