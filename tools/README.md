# Development tools

## Canonical local gate

Run `tools/check`.

The gate validates PHP and JavaScript syntax, the Base v1 public-contract boundary, Dictionary conformance and all product regressions.

## Conformance

Run `php tools/conformance.php`.

The check verifies expected runtime files, public Base boundaries, native CPT/taxonomy/meta structure, the machine-owned alphabet contract, Governance APIs and configurable rewrites.

## PHP syntax

```bash
find . -type f -name '*.php' -not -path './build/*' -print0 | xargs -0 -n1 php -l
```

## Release build

Run `tools/build-release`.

The builder validates release identity, requires a clean release-source tree, runs `tools/check`, validates the staged PHP and JavaScript runtime and creates `build/core-blueprint-dictionary-{version}.zip` with a SHA-256 sidecar.
